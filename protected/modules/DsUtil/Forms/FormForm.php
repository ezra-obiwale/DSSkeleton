<?php
/*
 */

namespace DsUtil\Forms;

use DScribe\Form\Form;

/**
 * Description of FormForm
 *
 * @author topman
 */
class FormForm extends Form {

	protected $models;
	protected $forms;
	protected $fieldsets;

	public function __construct(array $models, array $forms, array $fieldsets) {
		parent::__construct('form');
		$this->setAttribute('method', 'post');

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
				'label' => 'Extend Form',
				'emptyValue' => '-- Default --',
				'values' => $forms
			),
			'attributes' => array(
				'size' => 6,
			),
		));

		$this->add(array(
			'name' => 'method',
			'type' => 'select',
			'options' => array(
				'label' => 'Method',
				'values' => array('GET' => 'GET', 'POST' => 'POST'),
				'default' => 'POST',
			),
		));

		$this->add(array(
			'name' => 'model',
			'type' => 'select',
			'options' => array(
				'label' => 'Model',
				'emptyValue' => '-- None --',
				'values' => $models,
			),
			'attributes' => array(
				'size' => 6,
			),
		));

		$this->add(array(
			'name' => 'elements',
			'type' => 'textarea',
			'options' => array(
				'label' => 'Elements'
			),
			'attributes' => array(
				'size' => 6,
			),
		));

		$this->add(array(
			'name' => 'fieldsets[]',
			'type' => 'select',
			'options' => array(
				'label' => 'Add Fieldsets',
				'values' => $fieldsets,
			),
			'attributes' => array(
				'multiple' => 'multiple',
				'size' => 6,
			),
		));

		$this->add(array(
			'name' => 'submitLabel',
			'type' => 'text',
			'options' => array(
				'label' => 'Submit Button',
				'value' => 'Submit'
			),
			'attributes' => array(
				'id' => 'submit',
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
