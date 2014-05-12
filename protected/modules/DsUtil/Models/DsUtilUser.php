<?php
/*
 */

namespace DsUtil\Models;

use DScribe\Core\AUser;

/**
 * Description of DsUtilUser
 *
 * @author topman
 */
class DsUtilUser extends AUser {

	protected $username;
	protected $password;

	public function getUsername() {
		return $this->username;
	}

	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}

	public function getId() {
		return -1;
	}

	public function getRole() {
		return 'utilizer';
	}

}
