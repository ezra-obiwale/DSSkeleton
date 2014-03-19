<?php
/*
 */

namespace DsUtil\Controllers;

/**
 * Description of ServicesController
 *
 * @author topman
 */
class ServicesController extends SuperController {

	public function indexAction($module) {
		return array(
			'module' => $module,
			'services' => $this->service->parseAllServices($module)
		);
	}

	public function newAction($module) {
		$this->service->getModulesService()->checkExists($module);
		$form = $this->service->getForm($module);
		if ($this->request->isPost()) {
			$form->setData($this->request->getPost());
			if ($form->isValid() && $this->service->createService($module, $form->getData())) {
				$this->flash()->setSuccessMessage('Service created successfully');

				$action = isset($this->request->getPost()->saveAndNew) ?
					'new' : 'index';
				$this->redirect('ds-util', 'services', $action, array($module));
			} else {
				$this->flash()->setErrorMessage('Create service failed');
			}
		}
		return array(
			'module' => $module,
			'form' => $form,
		);
	}

}
