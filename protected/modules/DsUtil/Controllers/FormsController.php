<?php
/*
 */

namespace DsUtil\Controllers;

/**
 * Description of FormsController
 *
 * @author topman
 */
class FormsController extends SuperController {

	public function accessRules() {
		return array_merge_recursive(array(
			array('deny', array(
				'actions' => 'index'
			))
		), parent::accessRules());
	}

	public function accessDenied($action, $args) {
		if ($action === 'index') {
			$this->redirect('ds-util', 'forms', 'new', $args);
		}
		return parent::accessDenied($action);
	}

	public function indexAction($module) {
		return array(
			'module' => $module,
			'forms' => $this->service->parseAllForms($module)
		);
	}

	public function newAction($module) {
		$this->service->getModulesService()->checkExists($module);
		$form = $this->service->getForm($module);
		if ($this->request->isPost()) {
			$form->setData($this->request->getPost());
			if ($form->isValid() && $this->service->createForm($module, $form->getData())) {
				$this->flash()->setSuccessMessage('Form created successfully');

				$action = isset($this->request->getPost()->saveAndNew) ?
					'new' : 'index';
				$this->redirect('ds-util', 'forms', $action, array($module));
			} else {
				$this->flash()->setErrorMessage('Create form failed');
			}
		}
		return array(
			'module' => $module,
			'form' => $form,
		);
	}

}
