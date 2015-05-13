<?php
App::uses('UserManagementAppModel', 'UserManagement.Model');
/**
 * Group Model
 *
 * @property User $User
 */
class Group extends UserManagementAppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

    var $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'required' => true, 'allowEmpty' => false,
                'message' => 'Please enter a groupname.'),
            'unique_name' => array(
                'rule' => array('isUnique', 'name'),
                'message' => 'This group name is already in use.')
        )
    );


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'group_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
