<?php

/*
 */

namespace DsUtil\Controllers;

use DScribe\Core\AController;

/**
 * Description of SuperController
 *
 * @author topman
 */
class SuperController extends AController {

    public function accessRules() {
        return array(
            array('allow', array(
                    'id' => '-1'
                )),
            array('deny'),
        );
    }

    public function accessDenied($action, $args) {
        $this->flash()->setErrorMessage('Login now to continue');
        $this->redirect('ds-util', 'guest', 'login');
    }

    protected function init() {
        $this->layout = '2-columns';
    }
}
