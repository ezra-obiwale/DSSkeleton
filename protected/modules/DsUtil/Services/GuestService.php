<?php
/*
 */

namespace DsUtil\Services;

use DScribe\Core\AService;

/**
 * Description of GuestService
 *
 * @author topman
 */
class GuestService extends AService {

	protected $loginForm;

	protected function inject() {
		return array(
			'loginForm' => array(
				'class' => 'DsUtil\Forms\LoginForm'
			)
		);
	}

	public function getLoginForm() {
		return $this->loginForm;
	}

}
