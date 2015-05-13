<?php
App::uses('UserManagementAppModel', 'UserManagement.Model');
/**
 * Usermeta Model
 *
 * @property User $User
 */
class Usermeta extends UserManagementAppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
