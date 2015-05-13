<?php
App::uses('Usermeta', 'UserManagement.Model');

/**
 * Usermeta Test Case
 *
 */
class UsermetaTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.user_management.usermeta',
		'plugin.user_management.user'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Usermeta = ClassRegistry::init('UserManagement.Usermeta');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Usermeta);

		parent::tearDown();
	}

}
