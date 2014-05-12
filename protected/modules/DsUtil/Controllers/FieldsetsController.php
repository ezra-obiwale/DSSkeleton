<?php
/*
 */

namespace DsUtil\Controllers;

/**
 * Description of FieldsetsController
 *
 * @author topman
 */
class FieldsetsController extends SuperController {

	public function accessRules() {
		return array_merge_recursive(array(
			array('deny', array(
				'actions' => 'index'
			))
		), parent::accessRules());
	}

	public function accessDenied($action, $args) {
		if ($action === 'index') {
			$this->redirect('ds-util', 'fieldsets', 'new', $args);
		}
		return parent::accessDenied($action);
	}

	public function indexAction($module) {
		return array(
			'module' => $module,
			'fieldsets' => $this->service->parseAllFieldsets($module)
		);
	}

	public function newAction($module) {
		$this->service->getModulesService()->checkExists($module);
		$fieldset = $this->service->getFieldset($module);
		if ($this->request->isPost()) {
			$fieldset->setData($this->request->getPost());
			if ($fieldset->isValid() && $this->service->createFieldset($module, $fieldset->getData())) {
				$this->flash()->setSuccessMessage('Fieldset created successfully');

				$action = isset($this->request->getPost()->saveAndNew) ?
					'new' : 'index';
				$this->redirect('ds-util', 'fieldsets', $action, array($module));
			} else {
				$this->flash()->setErrorMessage('Create fieldset failed');
			}
		}
		return array(
			'module' => $module,
			'fieldset' => $fieldset,
		);
	}

}
