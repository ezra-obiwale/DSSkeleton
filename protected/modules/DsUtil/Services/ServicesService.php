<?php
/*
 */

namespace DsUtil\Services;

/**
 * Description of ServicesService
 *
 * @author topman
 */
class ServicesService extends SuperService {

	protected $form;

	protected function inject() {
		return array_merge(parent::inject(), array(
				'form' => array(
					'class' => 'DsUtil\Forms\ServiceForm',
					'params' => array(
						$this->parseForForm('services'),
						$this->parseForForm('models'),
						$this->parseForForm('forms')
					),
				),
			));
	}

	public function getForm() {
		return $this->form;
	}

	public function getModulesService() {
		return $this->modulesService;
	}

	public function parseAllServices($module) {
		$services = array();
		foreach ($this->getAll('services', $module) as $service) {
			$class = ucfirst($module) . '\Services\\' . $service;
			if (!class_exists($class)) {
				$services[$service] = array();
				continue;
			}

			if ($serviceClass = $this->loadClass($class)){
				$modelClass = $serviceClass->getModel();
				if ($modelClass !== null)
					$modelClass = get_class($modelClass);

				$services[$service] = array(
					'modelClass' => $modelClass,
					'form' => null,
				);

				if (method_exists($serviceClass, 'getForm')) {
					$form = $serviceClass->getForm($module);
					if ($form !== null)
						$services[$service]['form'] = get_class($form);
				}
			}
		}

		return $services;
	}

	public function createService($module, \Object $data) {
		$extend = (!empty($data->extend)) ? $data->extend : 'DScribe\Core\AService';
		$info = pathinfo(str_replace('\\', '/', $extend));

		ob_start();
		?>

namespace <?= ucfirst($module) ?>\Services;

use <?= $extend ?>;
<?= (isset($data->autoGen)) ? 'use ' . ucfirst($module) . '\Models\\' . $data->name . ';' . "\n" : '' ?>

class <?= ucfirst($data->name) ?>Service extends <?= $info['filename'] ?> {
<?php if (!isset($data->autoGen)): ?>
	<?php if (!empty($data->form)): ?>

	protected $form;
	<?php endif; ?>
	<?php if (!empty($data->modelClass)): ?>

	protected function init() {
		$this->setModel(new \<?= $data->modelClass ?>);
	}
	<?php endif; ?>
	<?php if (!empty($data->form)): ?>

	protected function inject() {
		return array(
			'form' => array(
				'class' => '<?= $data->form ?>'
			)
		);
	}

	public function getForm() {
		return $this->form;
	}
	<?php endif; ?>
<?php else: ?>

	protected $form;

	/**
	 * Inject form into the service
	 * @return array
	 */
	protected function inject() {
		return array(
			'form' => array(
				'class' => '<?= ucfirst($module) ?>\Forms\<?= $data->name ?>Form'
			),
		);
	}

	/**
	 * Allow public access to form
	 * @return \<?= ucfirst($module) ?>\Forms\<?= $data->name ?>Form
	 */
	public function getForm() {
		return $this->form;
	}

	/**
	 * Fetch all data in the database
	 * return array
	 */
	public function fetchAll() {
		return $this->repository->fetchAll();
	}

	/**
	 * Find a row from database
	 * @param mixed $id Id to fetch with
	 * @return mixed
	 */
	public function findOne($id) {
		$this->model = $this->repository->findOne($id);
		return $this->model;
	}

	/**
	 * Inserts data into the database
	 * @param \<?= ucfirst($module) ?>\Models\<?= ucfirst($data->name) ?>

	 * return boolean
	 */
	public function create(<?= ucfirst($data->name) ?> $model) {
		$this->repository->insert($model)->execute();
		return $this->flush();
	}

	/**
	 * Saves data into the database
	 * @param \<?= ucfirst($module) ?>\Models\<?= ucfirst($data->name) ?>

	 * return boolean
	 */
	public function save(<?= ucfirst($data->name) ?> $model) {
		$this->repository->update($model, 'id')->execute();
		return $this->flush();
	}

	/**
	 * Deletes data from the database
	 * return boolean
	 */
	public function delete() {
		$this->repository->delete($this->model)->execute();
		return $this->flush();
	}
<?php endif; ?>

}
		<?php

		if (!is_dir(MODULES . $module . DIRECTORY_SEPARATOR . 'Services'))
			mkdir(MODULES . $module . DIRECTORY_SEPARATOR . 'Services');

		if (isset($data->overWrite) || (!isset($data->overWrite) && !is_readable(MODULES . $module . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . $data->name . 'Service.php')))
			return (file_put_contents(MODULES . $module . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . $data->name . 'Service.php', '<' . '?php' . "\n" . ob_get_clean()));
		return true;
	}

}
