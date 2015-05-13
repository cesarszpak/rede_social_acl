<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $userinfo;
    public $uses=array('UserManagement.User');
    public $components = array(
        'Auth' => array(
            'authError' => 'Only logged in users are allowed to access that. Please login to continue.',
            'authenticate' => array(
                'Form' => array(
                    'passwordHasher' => 'Blowfish',
                    'scope'=>array('User.group_id'=>1)
                )
            )
        ),
        'Session','UserManagement.GroupPermissionAcl'
    );
    public $helpers = array('Html', 'Form', 'Session');

    public function beforeFilter() {

        $is_allowed=0;

        //Configure AuthComponent
        $this->Auth->loginAction = array(
            'plugin'=>'UserManagement',
            'controller' => 'Users',
            'action' => 'login'
        );
        $this->Auth->logoutRedirect = array(
            'plugin'=>'UserManagement',
            'controller' => 'Users',
            'action' => 'login'
        );
        $this->Auth->loginRedirect = array(
            'plugin'=>'UserManagement',
            'controller' => 'Users',
            'action' => 'dashboard'
        );

        if ($this->Auth->User()) {

            $is_allowed=$this->GroupPermissionAcl->is_allowed($this->request->params['plugin'],$this->request->params['controller'],$this->request->params['action']);

            if($is_allowed == 0){

                $this->Session->setFlash(__('You don\'t have the rights to access this page.'));
                $this->redirect($this->Auth->logout());

            }else {
                /*here it goes*/

                //Sets user information in global variables
                $this->userinfo=$this->Auth->User();//$this->userinfo can be access in every controller if users in logged in.
                $this->set('userinfo',$this->Auth->User());// $userinfo has the user login details which is accessed in every view.It can also be accessed in every controller with $this->viewVars['userinfo'];
            }

        }
    }
}
