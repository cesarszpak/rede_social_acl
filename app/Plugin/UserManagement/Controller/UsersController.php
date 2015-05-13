<?php
App::uses('UserManagementAppController', 'UserManagement.Controller');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends UserManagementAppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
    public $uses=array('UserManagement.User','UserManagement.Group','UserManagement.GroupPermission');
    public $helpers=array('UserManagement.Permission');

    public function beforeFilter() {
        parent::beforeFilter();
        // For CakePHP 2.1 and up
        $this->Auth->allow('login','logout');
    }

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->User->delete()) {
			$this->Session->setFlash(__('The user has been deleted.'));
		} else {
			$this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

    /**
     * login method
     *
     */
    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Session->setFlash(__('Your username or password was incorrect.'));
            return $this->redirect(array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'login'));
        }
        if ($this->Auth->login()) {
            return $this->redirect($this->Auth->redirectUrl());
        }
    }
    /**
     * logout method
     *
     */
    public function logout() {
        $this->Session->setFlash('Successfully logged out');
        $this->redirect($this->Auth->logout());
    }
    /**
 * dashboard method
 *
 */
    public function dashboard() {
        //Leave empty for now.
    }
    /**
     * permissions method
     *
     */
    public function permissions() {

        $allControllers=$this->GroupPermissionAcl->_getAllControllers();
        $pluginsControllers=$this->GroupPermissionAcl->_getAllPluginController();
        $groups=$this->Group->find('list',array('conditions'=>array('Group.access_code != 0'),'fields'=>array('access_code','name')));


        $this->GroupPermission->virtualFields=array(
            'plugin'=>'GroupPermission.plugin',
            'name'=>"Select IF(plugin <> '', CONCAT(GroupPermission.plugin,',',GroupPermission.controller,',',GroupPermission.action),CONCAT(GroupPermission.controller,',',GroupPermission.action))"
        );
        $group_permission=$this->GroupPermission->find('all',array('conditions'=>array(),'fields'=>array('id','name','sum_of_group_access_code','plugin')));

        $gPermissions = Hash::combine(
            $group_permission,
            array('%s', '{n}.GroupPermission.name', ''),'{n}.GroupPermission.sum_of_group_access_code'
        );

        $this->set(compact('allControllers','pluginsControllers','groups','gPermissions'));
    }

    /*Ajax save of the group permissions
     *
     * */
    public function ajaxPermissionSave(){
        if($this->request->is('ajax')){
            $data=$this->request->data['info'];
            $checked=$this->request->data['is_checked'];
            if($this->GroupPermissionAcl->_savePermission($data,$checked)){
                echo "success";
            }
        }

        exit;
    }

}
