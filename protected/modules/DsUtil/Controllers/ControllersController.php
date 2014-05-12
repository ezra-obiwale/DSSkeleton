<?php
/*
 */

namespace DsUtil\Controllers;

/**
 * Description of ControllersController
 *
 * @author topman
 */
class ControllersController extends SuperController {

	public function indexAction($module) {
		return array(
			'module' => $module,
			'controllers' => $this->service->parseAllControllers($module)
		);
	}

	public function newAction($module) {
		$this->service->getModulesService()->checkExists($module);
		$form = $this->service->getForm($module);
		if ($this->request->isPost()) {
			$form->setData($this->request->getPost());
			if ($form->isValid() && $this->service->createController($module, $form->getData())) {
				$this->flash()->setSuccessMessage('Controller created successfully');

				$action = isset($this->request->getPost()->saveAndNew) ?
					'new' : 'index';
				$this->redirect('ds-util', 'controllers', $action, array($module));
			} else {
				$this->flash()->setErrorMessage('Create controller failed');
			}
		}
		return array(
			'module' => $module,
			'form' => $form,
		);
	}

	public function makeDefaultControllerAction($module, $controller) {
		if ($this->service->makeDefaultController($module, $controller)) {
			$this->flash()->setSuccessMessage('Controller "' . $controller . '" is now the default controller for this module');
		} else {
			$this->flash()->setErrorMessage('Make default failed. Please retry later');
		}
		sleep(3);
		$this->redirect('ds-util', 'controllers', 'index', array($module));
	}

	public function makeDefaultActionAction($module, $action) {
		if ($this->service->makeDefaultAction($module, $action)) {
			$this->flash()->setSuccessMessage('Action "' . $action . '" is now the default action for this module');
		} else {
			$this->flash()->setErrorMessage('Make default failed. Please retry later');
		}
		sleep(3);
		$this->redirect('ds-util', 'controllers', 'index', array($module));
	}

}
