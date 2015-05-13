Read the documentation before installing the UserManagement Plugin

1.Add this line in the app/Config/bootstrap.php:-
    CakePlugin::load('UserManagement', array('bootstrap' => true, 'routes' => true));
    /***** This line will load the plugin *****/

2.If you want login page as the landing page then change line no 27 in app/Config/routes.php:-
    Router::connect('/', array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'login'));

3. Add this in app/Controller/AppController.php inside the class:-
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

                $is_allowed=false;

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

4. Replace you app/View/Layouts/default.ctp with this default.ctp . And then customize of your own.
(OR)
add this line {echo $this->Session->flash('auth');} below this line { echo $this->Session->flash(); }
and make sure you have included jquery.min.js in the default.ctp

5.Simple ACL is integrated in this plugin. Administrator can access everything. If all the checkbox is unchecked for a method then no one can access that method accept administrator. If you want to give access to one role and no access to all other roles then you need to check the box for that role to whom you want to give access and leave empty for others.

*** Main configuration file is located in app/Plugin/UserManagement/Config/bootstrap.php. All configuration for social apps are located here.



Successfully installed
