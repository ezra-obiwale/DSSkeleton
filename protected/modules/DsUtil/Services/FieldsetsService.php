<?php
/*
 */

namespace DsUtil\Services;

use Exception,
	Object,
	Util;

/**
 * Description of FieldsetsService
 *
 * @author topman
 */
class FieldsetsService extends SuperService {

	protected $form;

	protected function inject() {
		return array_merge(parent::inject(), array(
				'form' => array(
					'class' => 'DsUtil\Forms\FieldsetForm',
					'params' => array(
						$this->parseForForm($this->getAll('models')),
						$this->parseForForm($this->getAll('fieldsets')),
					)
				),
			));
	}

	public function getFieldset() {
		return $this->form;
	}

	public function getModulesService() {
		return $this->modulesService;
	}

	public function parseAllFieldsets($module) {
		$fieldsets = array();
		foreach ($this->getAll('fieldsets', $module) as $nm => $fieldset) {
			$class = ucfirst($module) . '\Fieldsets\\' . $fieldset;
			if (!class_exists($class)) {
				$fieldsets[$fieldset] = array();
				continue;
			}

			if ($refClass = $this->loadClass($class, true)) {
				$contructor = $refClass->getConstructor();
				$params = $contructor->getParameters();
				$args = array();
				foreach ($params as $param) {
					if ($param->isDefaultValueAvailable()) {
						$args[] = $param->getDefaultValue();
					} else if ($param->allowsNull()) {
						$args[] = null;
					} else if ($param->isArray()) {
						$args[] = array();
					} else if ($param->isPassedByReference()) {
						die('in');
					} else if ($param->canBePassedByValue()) {
						$args[] = null;
					}
					else {
						die(print_r(get_class_methods($param)));
					}
				}

				$fieldsets[$fieldset] = $fieldsetClass->getModelClass();

				$fieldsetClass = $refClass->newInstanceArgs($args);
				if ($fieldsetClass->getElements()) {
					foreach ($fieldsetClass->getElements() as $element) {
						$fieldsets[$fieldset][$fieldsetClass->getModelClass()][] = $element->name;
					}
				}
			}
		}

		return $fieldsets;
	}

	public function createFieldset($module, Object $data) {
		$data->elements = str_replace(',,', '', $data->elements);

		if (!empty($data->model)) {
			try {
				$model = ucfirst($module) . '\Models\\' . $data->model;
				if (class_exists($model)) {
					$modelClass = new $model;
					$data->elements = join(',', array_keys($modelClass->toArray(true)));
				}
			} catch (Exception $ex) {
				throw new Exception($ex->getMessage());
			}
		}

		$extend = (!empty($data->extend)) ? $data->extend : 'DScribe\Fieldset\Fieldset';
		$info = pathinfo(str_replace('\\', '/', $extend));
		ob_start();
		?>

namespace <?= ucfirst($module) ?>\Fieldsets;

use <?= $extend ?>;

class <?= ucfirst($data->name) ?> extends <?= $info['filename'] ?> {

	public function __construct() {
		parent::__construct();
	<?php if (!empty($data->model)): ?>

		$this->setModel(new \<?= $data->model ?>);
		<?php
		$modClass = new $data->model();
		$data->elements = join(',', array_keys($modClass->toArray(true)));
	endif;
	foreach (explode(',', $data->elements) as $element):
		if (empty($element))
			continue;
		$element = Util::_toCamel($element)
		?>

		$this->add(array(
			'name' => '<?= $element ?>',
			'type' => 'text',
			'options' => array(
				'label' => '<?= ucwords(Util::camelToSpace($element)) ?>'
			),
		));
	<?php endforeach; ?>
	<?php
	if (!empty($data->fieldsets)):
		foreach ($data->fieldsets as $fieldset):
			$info = pathinfo(str_replace('\\', '/', $fieldset));
			$name = (substr($info['filename'], strlen($info['filename']) - 8)) ? substr($info['filename'], 0, strlen($info['filename']) - 8) : $info['filename'];
			?>

			$this->add(array(
				'name' => '<?= Util::camelTo_($name) ?>',
				'type' => 'fieldset',
				'value' => new <?= $fieldset ?>,
			));
			<?php
		endforeach;
	endif;
	?>

	}

	public function filters() {
		return array(
		<?php
		foreach (explode(',', $data->elements) as $element):
			if (empty($element))
				continue;

			$element = Util::_toCamel($element)
			?>

			'<?= $element ?>' => array(
				'required' => true,
			),
		<?php endforeach; ?>

		);
	}
}
		<?php
		if (!is_dir(MODULES . $module . DIRECTORY_SEPARATOR . 'Fieldsets'))
			mkdir(MODULES . $module . DIRECTORY_SEPARATOR . 'Fieldsets');

		if (isset($data->overWrite) || (!isset($data->overWrite) && !is_readable(MODULES . $module . DIRECTORY_SEPARATOR . 'Fieldsets' . DIRECTORY_SEPARATOR . $data->name . '.php')))
			return (file_put_contents(MODULES . $module . DIRECTORY_SEPARATOR . 'Fieldsets' . DIRECTORY_SEPARATOR . $data->name . '.php', '<' . '?php' . "\n" . ob_get_clean()));
		return true;
	}

}
