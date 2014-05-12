<?php
/*
 */

namespace DsUtil\Forms;

use DScribe\Form\Form;

/**
 * Description of ControllerForm
 *
 * @author topman
 */
class ControllerForm extends Form {

	private $layouts;
	private $controllers;

	public function __construct() {
		parent::__construct('controller');
		$this->setAttribute('method', 'post');
	}

	public function setControllers(array $controllers) {
		$this->controllers = $controllers;
		return $this;
	}

	public function setLayouts(array $layouts) {
		$this->layouts = $layouts;
		return $this;
	}

	public function doConstruct() {
		$this->add(array(
			'name' => 'name',
			'type' => 'text',
			'options' => array(
				'label' => 'Name'
			),
		));

		$this->add(array(
			'name' => 'extend',
			'type' => 'select',
			'options' => array(
				'label' => 'Extend Controller',
				'emptyValue' => 'Default (AController)',
				'values' => $this->controllers,
			),
			'attributes' => array(
				'multiple' => 'multiple',
				'size' => 6
			)
		));

		$this->add(array(
			'name' => 'actions',
			'type' => 'text',
			'options' => array(
				'label' => 'Actions',
			),
			'attributes' => array(
				'placeholder' => 'comma separated'
			),
		));

		$this->add(array(
			'name' => 'noCache',
			'type' => 'text',
			'options' => array(
				'label' => 'Actions not to cache'
			),
			'attributes' => array(
				'placeholder' => 'comma separated'
			),
		));

		$this->add(array(
			'name' => 'layout',
			'type' => 'select',
			'options' => array(
				'label' => 'Layout',
				'emptyValue' => '-- None --',
				'values' => $this->layouts,
			),
		));

		$this->add(array(
			'name' => 'overWrite',
			'type' => 'checkbox',
			'options' => array(
				'label' => 'Overwrite if exists'
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
