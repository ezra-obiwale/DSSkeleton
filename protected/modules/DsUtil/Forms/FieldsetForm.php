<?php
/*
 */

namespace DsUtil\Forms;

use DScribe\Form\Form;

/**
 * Description of FieldsetForm
 *
 * @author topman
 */
class FieldsetForm extends Form {

	public function __construct($models, $fieldsets) {
		parent::__construct('fieldset');
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
				'label' => 'Extend Fieldset',
				'emptyValue' => '-- Default --',
				'values' => $fieldsets
			),
			'attributes' => array(
				'size' => 6,
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
		));

		$this->add(array(
			'name' => 'fieldsets[]',
			'type' => 'select',
			'options' => array(
				'label' => 'Add Fieldsets',
				'emptyValue' => '-- None --',
				'values' => $fieldsets,
			),
			'attributes' => array(
				'multiple' => 'multiple',
				'size' => 6,
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
