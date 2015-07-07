<?php

namespace App\Controllers;

use App\Models\AdminUser,
	DScribe\Core\AController;

class IndexController extends AController {

	protected function init() {
		$this->layout = '1-column';
	}

	public function accessRules() {
		return array(
			array('deny', array(
					'role' => 'guest',
					'actions' => 'admin'
			)),
		);
	}

	public function indexAction() {

	}

	public function adminAction() {
		$this->layout = '2-columns';
	}

	public function loginAction() {
		$form = $this->service->getLoginForm();
		if ($this->request->isPost()) {
			$form->setData($this->request->getPost());
			if ($form->isValid() && $form->getData()->username == 'admin' && $form->getData()->password == 'admin') {
				$this->resetUserIdentity(new AdminUser());
				$this->redirect('app', 'index', 'admin');
			}
			$this->flash()->setErrorMessage('Invalid login details. Please try again');
		}

		return $this->view->variables(array(
				'form' => $form,
			));
	}

	public function logoutAction() {
		$this->resetUserIdentity();
		$this->redirect('app', 'index', 'login');
	}

}
