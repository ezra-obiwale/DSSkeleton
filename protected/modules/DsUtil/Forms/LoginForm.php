<?php
/*
 */

namespace DsUtil\Forms;

use DScribe\Form\Form;

/**
 * Description of LoginForm
 *
 * @author topman
 */
class LoginForm extends Form {

	public function __construct() {
		parent::__construct('login');
		$this->setAttribute('method', 'post');

		$this->add(array(
			'name' => 'username',
			'type' => 'text',
			'options' => array(
				'label' => 'Username'
			),
		));

		$this->add(array(
			'name' => 'password',
			'type' => 'password',
			'options' => array(
				'label' => 'Password'
			),
		));

		$this->add(array(
			'name' => 'csrf',
			'type' => 'hidden',
		));

		$this->add(array(
			'name' => 'submit',
			'type' => 'submit',
			'options' => array(
				'value' => 'Login'
			),
			'attributes' => array(
				'class' => 'btn btn-success'
			),
		));
	}

}
