<?php
/*
 */

namespace DsUtil\Controllers;

/**
 * Description of ModelsController
 *
 * @author topman
 */
class ModelsController extends SuperController {

	public function indexAction($module) {
		return array(
			'module' => $module,
			'models' => $this->service->parseAllModels($module)
		);
	}

	public function newAction($module) {
		$this->service->getModulesService()->checkExists($module);
		$form = $this->service->getForm();
		if ($this->request->isPost()) {
			$form->setData($this->request->getPost());
			if ($form->isValid() && $this->service->createModel($module, $form->getData())) {
				$msg = 'Model created successfully.';
				if (isset($form->getData()->dbs))
					$msg .= ' All properties have been created as "string" with size "220"';
				$this->flash()->setSuccessMessage($msg);

				$action = isset($this->request->getPost()->saveAndNew) ?
					'new' : 'index';
				$this->redirect('ds-util', 'models', $action, array($module));
			} else {
				$this->flash()->setErrorMessage('Create model failed');
			}
		}
		return array(
			'module' => $module,
			'form' => $form,
		);
	}

	public function autoGenAction($module, $overWrite = null, $type = null, $model = null) {
		if ($this->service->autoGen($module, $overWrite, $type, $model)) {
			$msg = (!$model) ? 'Controllers created successfully' : $model . ' Controller and views, Service, and Form created successfully';
			$this->flash()->setSuccessMessage($msg);
		} else {
			$msg = (!$model) ? 'Create controllers failed' : 'Existing file ignored. Others/None created';
			$this->flash()->setErrorMessage($msg);
		}

		$this->redirect('ds-util', 'models', 'index', array($module));
	}

}
