<?php
/*
 */

namespace DsUtil\Forms;

use DScribe\Form\Form;

/**
 * Description of ModuleForm
 *
 * @author topman
 */
class ModuleForm extends Form {

	public function __construct() {
		parent::__construct('module');
		$this->setAttribute('method', 'post');

		$this->add(array(
			'name' => 'name',
			'type' => 'text',
			'options' => array(
				'label' => 'Name'
			),
		));

		$this->add(array(
			'name' => 'activate',
			'type' => 'checkbox',
			'options' => array(
				'label' => 'Activate',
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
				'value' => 'Save'
			),
			'attributes' => array(
				'class' => 'btn btn-success'
			),
		));
	}

	public function filters() {
		return array(
			'name' => array(
				'required' => true,
				'NotEmpty' => array()
			),
		);
	}

}
