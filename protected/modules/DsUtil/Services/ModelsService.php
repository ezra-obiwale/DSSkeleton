<?php
/*
 */

namespace DsUtil\Services;

/**
 * Description of ModelsService
 *
 * @author topman
 */
class ModelsService extends SuperService {

	protected $controllersService;
	protected $servicesService;
	protected $formsService;
	protected $form;

	protected function inject() {
		return array_merge(parent::inject(), array(
				'controllersService' => array(
					'class' => 'DsUtil\Services\ControllersService'
				),
				'servicesService' => array(
					'class' => 'DsUtil\Services\ServicesService'
				),
				'formsService' => array(
					'class' => 'DsUtil\Services\FormsService'
				),
				'form' => array(
					'class' => 'DsUtil\Forms\ModelForm',
					'params' => array($this->parseModelsForForm()),
				),
			));
	}

	private function parseModelsForForm() {
		return $this->parseForForm($this->getAll('models'));
	}

	public function getForm() {
		return $this->form;
	}

	public function getModulesService() {
		return $this->modulesService;
	}

	public function getControllersService() {
		return $this->controllersService;
	}

	public function parseAllModels($module) {
		$models = array();
		foreach ($this->getAll('Models', $module) as $model) {
			$class = ucfirst($module) . '\Models\\' . $model;
			if (!class_exists($class)) {
				$models[$model] = array();
				continue;
			}

			$refClass = new \ReflectionClass($class);
			$models[$model] = array();
			foreach ($refClass->getProperties() as $property) {
				if ($property->name === '_tableName')
					continue;
				$models[$model][] = $property->name;
			}
		}

		return $models;
	}

	public function createModel($module, \Object $data) {
		$properties = array();
		if (!empty($data->properties))
			$properties = explode(',', $data->properties);

		$extend = (!empty($data->extend)) ? $data->extend : ((isset($data->dbs)) ? 'DScribe\Core\AModel' : '');
		ob_start();
		?>

namespace <?= ucfirst($module) ?>\Models;

use <?= (!empty($extend)) ? $extend : 'DScribe\Core\IModel' ?>;
<?php
if (!empty($extend)):
	$info = pathinfo(str_replace('\\', '/', $extend));
endif;
?>

class <?= ucfirst($data->name) ?> <?= (isset($info)) ? 'extends ' . $info['filename'] : '' ?> <?= (!isset($info)) ? 'implements IModel' : '' ?> {
<?php foreach ($properties as $property): if (empty($property)) continue; ?>
	<?php if (isset($data->dbs)): ?>

	/**
	* @DBS\String (size="220")
	*/
	<?php endif; ?>
protected $<?= \Util::_toCamel($property) ?>;
<?php endforeach; ?>
<?php if (!empty($extend) && !empty($data->tableName) && $data->tableName !== \Util::camelTo_($data->name)): ?>

	public function __construct() {
		$this->setTableName('<?= $data->tableName ?>');
	}
<?php elseif (empty($extend)): ?>

	public function getTableName() {
		return \Util::camelTo_(str_replace('<?= ucfirst($module) ?>\Models', '', get_class()));
	}

	public function populate(array $data) {
		foreach ($data as $property => $value) {
			if (property_exists($this, $property)) {
				$this->$property = $value;
			}
		}
		return $this;
	}

	public function toArray() {
		return get_object_vars($this);
	}
<?php endif; ?>
<?php foreach ($properties as $property): ?>

	public function set<?= ucfirst(\Util::_toCamel($property)) ?>($<?= \Util::_toCamel($property) ?>) {
		$this-><?= \Util::_toCamel($property) ?> = $<?= \Util::_toCamel($property) ?>;
		return $this;
	}

	public function get<?= ucfirst(\Util::_toCamel($property)) ?>() {
		return $this-><?= \Util::_toCamel($property) ?>;
	}
<?php endforeach; ?>

}

		<?php
		if (!is_dir(MODULES . $module . DIRECTORY_SEPARATOR . 'Models'))
			mkdir(MODULES . $module . DIRECTORY_SEPARATOR . 'Models');

		if (isset($data->overWrite) || (!isset($data->overWrite) && !is_readable(MODULES . $module . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $data->name . '.php')))
			return file_put_contents(MODULES . $module . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $data->name . '.php', '<' . '?php' . "\n" . ob_get_clean());

	}

	public function autoGen($module, $overWrite, $type, $model = null) {
		if ($model) {
			$allModels = array($model);
		} else {
			$allModels = $this->getAll('models', $module);
		}

		foreach ($allModels as $model) {
			$data = new \Object(array(
					'name' => $model,
					'autoGen' => true,
				));
			if ($overWrite == 1)
				$data->add(array('overWrite' => 'on'));

			switch ($type) {
				case 'all':
				case 'controllers':
					if (!$this->controllersService->createController($module, $data, ($type === 'all')))
						return false;
					if ($type !== 'all')
						break;
				case 'views':
					if ($type !== 'all' && !$this->controllersService->createActionViews($module, $data, explode(',', 'index,new,edit,view,delete')))
						return false;
					if ($type !== 'all')
						break;
				case 'services':
					if (!$this->servicesService->createService($module, $data))
						return false;
					if ($type !== 'all')
						break;
				case 'forms':
					$data->model = ucfirst($module) . '\Models\\' . $data->name;
					$data->name .= 'Form';
					$data->method = 'post';
					$data->elements = '';
					$data->submitLabel = 'Save';
					if (!$this->formsService->createForm($module, $data))
						return false;
					if ($type !== 'all')
						break;
			}
		}

		return true;
	}

}
