<?php

namespace App\Services;

use App\Forms\LoginForm,
	dScribe\Core\AService;

class IndexService extends AService {

	protected $loginForm;

	protected function init() {
		
	}

	public function getLoginForm() {
		return new LoginForm;
	}

}
