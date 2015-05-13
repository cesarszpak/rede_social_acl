<?php
App::uses('UserManagementAppModel', 'UserManagement.Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
/**
 * User Model
 *
 * @property Group $Group
 */
class User extends UserManagementAppModel {

    var $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'required' => true, 'allowEmpty' => false,
                'message' => 'Please enter a username.'),
            'unique_username' => array(
                'rule' => array('isUnique', 'username'),
                'message' => 'This username is already in use.')
        ),
        'password' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => 'Please enter a password.'
            )
        )
    );



    //The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

    public function beforeSave($options = array()) {
        if (!empty($this->data[$this->alias]['password'])) {
            $passwordHasher = new BlowfishPasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                $this->data[$this->alias]['password']
            );
        }else{
            unset($this->data[$this->alias]['password']);
        }
        return true;
    }
}
