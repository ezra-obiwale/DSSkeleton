<?php

/*
 */

namespace DsUtil\Controllers;

/**
 * Description of ModulesController
 *
 * @author topman
 */
class ModulesController extends SuperController {

    public function indexAction() {
        return array(
            'modules' => $this->service->getModules(),
        );
    }

    public function activateAction($module) {
        if ($this->service->activateModule($module)) {
            $this->flash()->setSuccessMessage('Module "' . $module . '" has been activated');
            sleep(5);
        }
        else {
            $this->flash()->setErrorMessage('Module "' . $module . '" could not be activated. Please try again later');
        }
        $this->redirect('ds-util', 'modules', 'index');
    }

    public function deactivateAction($module) {
        if ($this->service->deactivateModule($module)) {
            $this->flash()->setSuccessMessage('Module "' . $module . '" has been deactivated');
            sleep(5);
        }
        else {
            $this->flash()->setErrorMessage('Module "' . $module . '" could not be deactivated. Please try again later');
        }
        $this->redirect('ds-util', 'modules', 'index');
    }

    public function newAction() {
        $form = $this->service->getModuleForm();
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid() && $this->service->createModule($form->getData())) {
                $this->flash()->setSuccessMessage('Module "' . $form->getData()->name . '" was created successfully');

                $action = isset($this->request->getPost()->saveAndNew) ?
                        'new' : 'index';
                $this->redirect('ds-util', 'modules', $action);
            }
            else {
                $this->flash()->setErrorMessage('Module "' . $form->getData()->name . '" could not be created');
            }
        }
        return array(
            'form' => $form,
        );
    }

    public function makeDefaultAction($module) {
        if ($this->service->makeDefault($module)) {
            $this->flash()->setSuccessMessage('Module "' . $module . '" is now the default for your application.
				Remember to set the default controller and action too.');
            sleep(3);
        }
        else {
            $this->flash()->setErrorMessage('Make default failed. Ensure the module is activated first, then retry.');
        }
        $this->redirect('ds-util', 'modules', 'index');
    }

}
