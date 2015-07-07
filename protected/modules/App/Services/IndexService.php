<?php

namespace App\Services;

use DScribe\Core\AService;

class IndexService extends AService {

	protected $loginForm;

	protected function init() {

	}

	protected function inject() {
		return array(
			'loginForm' => array(
				'class' => 'App\Forms\LoginForm',
			),
		);
	}

	public function getLoginForm() {
		return $this->loginForm;
	}

}
