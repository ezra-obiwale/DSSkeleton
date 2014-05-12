<?php
/*
 */

namespace DsUtil\Forms;

use DScribe\Form\Form;

/**
 * Description of ServiceForm
 *
 * @author topman
 */
class ServiceForm extends Form {

	public function __construct($services, $models, $forms) {
		parent::__construct('service');
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
				'label' => 'Extend Service',
				'labelAttributes' => array(
					'title' => 'A Service Class to extend instead of the default'
				),
				'emptyValue' => '-- Default --',
				'values' => $services,
			),
			'attributes' => array(
				'size' => 6,
			)
		));

		$this->add(array(
			'name' => 'modelClass',
			'type' => 'select',
			'options' => array(
				'label' => 'Model Class',
				'emptyValue' => '-- Default --',
				'values' => $models,
			),
			'attributes' => array(
				'size' => 6,
			)
		));

		$this->add(array(
			'name' => 'form',
			'type' => 'select',
			'options' => array(
				'label' => 'Inject Form',
				'labelAttributes' => array(
					'title' => 'Form to inject into the service'
				),
				'emptyValue' => '-- None --',
				'values' => $forms,
			),
			'attributes' => array(
				'size' => 6,
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
		);
	}

}
