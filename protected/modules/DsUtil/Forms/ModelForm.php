<?php
/*
 */

namespace DsUtil\Forms;

use DScribe\Form\Form;

/**
 * Description of ModelForm
 *
 * @author topman
 */
class ModelForm extends Form {

	public function __construct(array $models = array()) {
		parent::__construct('model');
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
				'label' => 'Extend Model',
				'emptyValue' => '-- Default --',
				'values' => $models
			),
			'attributes' => array(
				'size' => 6,
			),
		));

		$this->add(array(
			'name' => 'tableName',
			'type' => 'text',
			'options' => array(
				'label' => 'Table Name'
			),
			'attributes' => array(
				'id' => 'tableName'
			),
		));

		$this->add(array(
			'name' => 'properties',
			'type' => 'textarea',
			'options' => array(
				'label' => 'Properties',
			),
			'attributes' => array(
				'id' => 'properties'
			),
		));

		$this->add(array(
			'name' => 'dbs',
			'type' => 'checkbox',
			'options' => array(
				'default' => true,
				'label' => 'Use DBScribe Mapper'
			)
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
			'tableName' => array(
				'required' => true,
				'NotEmpty' => array()
			),
		);
	}

}
