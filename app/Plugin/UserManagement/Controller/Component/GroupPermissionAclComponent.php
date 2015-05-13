<?php
App::uses('Component', 'Controller');

class GroupPermissionAclComponent extends Component {


    /* check to see which action can be accessed by which group
     *
     */
    public function is_allowed($plugin,$controller,$action){

        $model='GroupPermission';
        App::import('Model','UserManagement.'.$model);
        $this->model = new $model();
		if($plugin==""){
            $plugin='';
        }
        $exists=$this->model->find('first',array(
            'conditions' => array(
                'plugin' => $plugin,
                'controller' => $controller,
                'action' => $action,
            ),
        ));
        $groupId=AuthComponent::user('group_id');
        $group='Group';
        $this->group = ClassRegistry::init($group);
        $checkGroup=$this->group->find('first',array(
            'conditions' => array(
                'id' => $groupId
            ),'fields'=>array('access_code')
        ));
        if(/*$checkGroup || */$checkGroup[$group]['access_code'] == 0){
            return 1;
        }
        //debug($plugin."==".$controller."--".$action);die();
        if(empty($exists)){
            //revoke access to everyone
            return 0;
        }else {

            $user_access=decbin($checkGroup['Group']['access_code']);
            $allowed_access=decbin($exists[$model]['sum_of_group_access_code']);

            if(strlen($user_access) < strlen($allowed_access)){
                $count=strlen($allowed_access) - strlen($user_access);
                for($i=1;$i<=$count;$i++){
                    $user_access= '0' . $user_access;
                }
            }
            if(strlen($allowed_access) < strlen($user_access)){
                $count=strlen($user_access) - strlen($allowed_access);
                for($i=1;$i<=$count;$i++){
                    $allowed_access= '0' . $allowed_access;
                }
            }


            $and=($allowed_access & $user_access);

            if(bindec($and) > 0)
                return 1;
            else
                return 0;
        }

    }

    /* Returns all access code
     *
     */

    public function getAccessCodes($access_code=null){

        if($access_code == 0)
            return array('0');

        $x=$access_code;
        $a=1;

        while ( $x >= $a){

            $x1=decbin($x);
            $a1=decbin($a);
            if(strlen($a1) < strlen($x1)){
                $count=strlen($x1) - strlen($a1);
                for($i=1;$i<=$count;$i++){
                    $a1= '0' . $a1;
                }
            }

            $and=($x1 & $a1);
            if($and > 0)
                $b[]=$a;
            $a=$a*2;
        }
        return array_values($b);
    }

    /**
     * Get the names of the controllers ...
     *
     * This function will get an array of the controller names
     *
     * @return array of plugin names.
     *
     *
     */
    function _getAllControllers($controller=null){
        // Load the ApplicationController (if there is one)
        App::import('Controller', 'AppController');

        $controllerClasses = App::objects('controller');
        $controllers=array();

        foreach ($controllerClasses as $controller) {
            if ($controller != 'AppController') {
                // Load the controller
                App::import('Controller', str_replace('Controller', '', $controller));

                // Load its methods / actions
                $actionMethods = get_class_methods($controller);
                foreach ($actionMethods as $key => $method) {

                    if ($method{0} == '_') {
                        unset($actionMethods[$key]);
                    }
                }

                $parentActions = get_class_methods('AppController');
                $controllers[str_replace('Controller', '', $controller)] = array_diff($actionMethods, $parentActions);

            }
        }
        return $controllers;
    }

    /**
     * Get the names of the plugin controllers ...
     *
     * This function will get an array of the plugin controller names
     *
     * @return array of plugin names.
     *
     *
     */
    function _getAllPluginController() {
        App::uses('Folder', 'Utility');
        $folder = & new Folder();
        $folder->cd(APP . 'Plugin');

        // Get the list of plugins
        $Plugins = $folder->read();
        $Plugins = $Plugins[0];
        $arr = array();

        // Loop through the plugins
        foreach ($Plugins as $pluginName) {
            // Change directory to the plugin
            $didCD = $folder->cd(APP . 'Plugin' . DS . $pluginName . DS . 'Controller');
            if ($didCD) {
                // Get a list of the files that have a file name that ends
                // with controller.php
                $files = $folder->findRecursive('.*Controller\.php');

                // Loop through the controllers we found in the plugins directory
                foreach ($files as $fileName) {
                    // Get the base file name
                    $file = basename($fileName);

                    // Get the controller name
                    //$file = Inflector::camelize(substr($file, 0, strlen($file) - strlen('Controller.php')));
                    if (!preg_match('/^' . Inflector::humanize($pluginName) . 'App/', $file)) {
                        $file = str_replace('.php', '', $file);

                        /// Now prepend the Plugin name ...
                        // This is required to allow us to fetch the method names.
                        $actionArray=$this->_getPluginControllerMethods(Inflector::humanize($pluginName).'.'.$file);
                        $arr[Inflector::humanize($pluginName)][str_replace('Controller', '', $file)] = $actionArray;
                        //$arr[] = Inflector::humanize($pluginName) . "." . $file;
                    }

                }
            }
        }


        return $arr;
    }
    function _isPlugin($ctrlName = null) {
        $arr = String::tokenize($ctrlName, '.');
        if (count($arr) > 1) {
            return true;
        } else {
            return false;
        }
    }
    function _getPluginName($ctrlName = null) {
        $arr = String::tokenize($ctrlName, '.');
        if (count($arr) == 2) {
            return $arr[0];
        } else {
            return false;
        }
    }

    function _getPluginControllerName($ctrlName = null) {
        $arr = String::tokenize($ctrlName, '.');
        if (count($arr) == 2) {
            return $arr[1];
        } else {
            return false;
        }
    }
    function _getPluginControllerMethods($controller=null){
        // Load the ApplicationController (if there is one)
        App::import('Controller', 'AppController');

        if($this->_isPlugin($controller)){
            App::uses($this->_getPluginControllerName($controller), $this->_getPluginName($controller).'.Controller');
            // Load its methods / actions
            $actionMethods = get_class_methods($this->_getPluginControllerName($controller));
            foreach ($actionMethods as $key => $method) {

                if ($method{0} == '_') {
                    unset($actionMethods[$key]);
                }
            }

            $parentActions = get_class_methods('AppController');
            $actions = array_diff($actionMethods, $parentActions);
        }
        return $actions;

    }

    /*Save group permission in database
     *
     * */

    function _savePermission($data,$checked){
        $model='GroupPermission';
        App::import('Model','UserManagement.'.$model);
        $this->model = new $model();
        $arr = String::tokenize($data, ',');
        $conditions=array();
        if(count($arr)>3){
            $plugin=$arr[0];
            $controller=$arr[1];
            $action=$arr[2];
            $code=$arr[3];
        }else{
            $plugin='';
            $controller=$arr[0];
            $action=$arr[1];
            $code=$arr[2];
        }
        $conditions=array('plugin' => $plugin,
            'controller' => $controller,
            'action' => $action
        );
        $exists=$this->model->find('first',array(
            'conditions' => $conditions
        ));
        if(empty($exists)){
            $data=array(
                $model=>array(
                    'plugin' => $plugin,
                    'controller' => $controller,
                    'action' => $action,
                    'sum_of_group_access_code' => $code
                )
            );
            $this->model->create();
            $this->model->save($data);
        }else {
            $ac_code=$this->getAccessCodes($exists[$model]['sum_of_group_access_code']);

                if($checked==="true" && !in_array($code,$ac_code)){
                    $code=$code + $exists[$model]['sum_of_group_access_code'];
                }else{
                    $code=$exists[$model]['sum_of_group_access_code']-$code;
                }
                $data=array(
                    $model=>array(
                        'plugin' => $plugin,
                        'controller' => $controller,
                        'action' => $action,
                        'sum_of_group_access_code' => $code
                    )
                );
                $this->model->id=$exists[$model]['id'];
                $this->model->save($data);

        }
        return true;
    }

}