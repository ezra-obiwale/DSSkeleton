<?php

namespace DsUtil\Controllers;

use DScribe\Core\AController,
    DsUtil\Models\DsUtilUser;

/**
 * Description of GuestController
 *
 * @author topman
 */
class GuestController extends AController {
    
    protected function init() {
        $this->layout = 'main';
    }

    public function indexAction() {
        return $this->view;
    }

    /* @todo register admin user */

    public function loginAction() {
        if ($this->userIdentity()->getUser()->getId() === -1 &&
                $this->userIdentity()->getUser()->getUsername() === $this->getConfig('modules', 'DsUtil', 'access', 'username') &&
                $this->userIdentity()->getUser()->getPassword() === $this->getConfig('modules', 'DsUtil', 'access', 'password')) {
            $this->redirect('ds-util', 'modules', 'index');
        }

        $form = $this->service->getLoginForm();
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                if ($form->getData()->username !== $this->getConfig('modules', 'DsUtil', 'access', 'username') ||
                        $form->getData()->password !== $this->getConfig('modules', 'DsUtil', 'access', 'password')) {
                    $this->flash()->setErrorMessage('Login failed: Username/Password invalid');
                }
                else {
                    $user = new DsUtilUser();
                    $user->setUsername($form->getData()->username)
                            ->setPassword($form->getData()->password);
                    $this->resetUserIdentity($user);
                    $this->redirect('ds-util', 'modules', 'index');
                }
            }
        }

        return $this->view->variables(array(
                    'form' => $form,
        ));
    }

    public function logoutAction() {
        $this->resetUserIdentity();
        $this->redirect('ds-util', 'guest', 'login');
    }

}