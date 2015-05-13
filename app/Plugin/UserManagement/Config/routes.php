<?php
/**
 * Routes configuration for UserManagement Plugin
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

//Routes for Users
Router::connect('/users', array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'index'));
Router::connect('/users/add', array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'add'));
Router::connect('/users/edit/*', array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'edit'));
Router::connect('/users/view/*', array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'view'));
Router::connect('/users/delete/*', array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'delete'));
Router::connect('/users/login', array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'login'));
Router::connect('/users/logout', array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'logout'));
Router::connect('/users/dashboard', array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'dashboard'));
Router::connect('/permissions', array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'permissions'));
Router::connect('/savepermission', array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'ajaxPermissionSave'));

//Routes for Groups
Router::connect('/groups', array('plugin'=>'UserManagement','controller' => 'Groups', 'action' => 'index'));
Router::connect('/groups/add', array('plugin'=>'UserManagement','controller' => 'Groups', 'action' => 'add'));
Router::connect('/groups/edit/*', array('plugin'=>'UserManagement','controller' => 'Groups', 'action' => 'edit'));
Router::connect('/groups/view/*', array('plugin'=>'UserManagement','controller' => 'Groups', 'action' => 'view'));
Router::connect('/groups/delete/*', array('plugin'=>'UserManagement','controller' => 'Groups', 'action' => 'delete'));

//Routes for Social Login
Router::connect('/facebookLogin', array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'facebookLogin'));
Router::connect('/connectFacebook', array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'connectFacebook'));
Router::connect('/twitterLogin', array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'twitterLogin'));
Router::connect('/twitter-callback', array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'callback'));
Router::connect('/linkedinLogin', array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'linkedinLogin'));
Router::connect('/googleLogin', array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'googleLogin'));