<?php
/*
 */

namespace DsUtil\Services;

/**
 * Description of FormsService
 *
 * @author topman
 */
class FormsService extends SuperService {

	protected $form;

	protected function inject() {
		return array_merge(parent::inject(), array(
				'form' => array(
					'class' => 'DsUtil\Forms\FormForm',
					'params' => array(
						$this->parseForForm('models'),
						$this->parseForForm('forms'),
						$this->parseForForm('fieldsets'),
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

	public function parseAllForms($module) {
		$forms = array();
		foreach ($this->getAll('forms', $module) as $form) {
			$class = ucfirst($module) . '\Forms\\' . $form;
			if (!class_exists($class)) {
				$forms[$form] = array();
				continue;
			}

			if ($formClass = $this->loadClass($class)){
				foreach ($formClass->getElements() as $element) {
					$forms[$form][$formClass->getModelClass()][] = $element->name;
				}
			}
		}

		return $forms;
	}

	public function createForm($module, \Object $data) {
		$data->elements = str_replace(',,', '', $data->elements);

		if (!empty($data->model)) {
			try {
				$model = ucfirst($module) . '\Models\\' . $data->model;
				if (class_exists($model)) {
					$modelClass = new $model;
					$data->elements = join(',', array_keys($modelClass->toArray(true)));
				}
			} catch (\Exception $ex) {
				throw new \Exception($ex->getMessage());
			}
		}

		$extend = (!empty($data->extend)) ? $data->extend : 'DScribe\Form\Form';
		$info = pathinfo(str_replace('\\', '/', $extend));
		ob_start();
		?>

namespace <?= ucfirst($module) ?>\Forms;

use <?= $extend ?>;

class <?= ucfirst($data->name) ?> extends <?= $info['filename'] ?> {

	public function __construct() {
		parent::__construct('<?= lcfirst($data->name) ?>');
	<?php if (!empty($data->model)): ?>

		$this->setModel(new \<?= $data->model ?>);
		<?php
		$modClass = new $data->model();
		$data->elements = join(',', array_keys($modClass->toArray(true)));
	endif;
	?>

		$this->setAttribute('method', '<?= $data->method ?>');
	<?php
	foreach (explode(',', $data->elements) as $element):
		if (empty($element))
			continue;
		$element = \Util::_toCamel($element)
		?>

		$this->add(array(
			'name' => '<?= $element ?>',
			'type' => 'text',
			'options' => array(
				'label' => '<?= ucwords(\Util::camelToSpace($element)) ?>'
			),
			'attributes' => array(
				'maxLength' => 220
			)
		));
	<?php endforeach; ?>
	<?php
	if (!empty($data->fieldsets)):
		foreach ($data->fieldsets as $fieldset):
			$info = pathinfo(str_replace('\\', '/', $fieldset));
			?>

		$this->add(array(
			'name' => '<?= str_replace('Form', '', $info['filename']) ?>',
			'type' => 'fieldset',
			'value' => new <?= $fieldset ?>,
		));
			<?php
		endforeach;
	endif;
	?>

		$this->add(array(
			'name' => 'csrf',
			'type' => 'hidden'
		));

		$this->add(array(
			'name' => 'submit',
			'type' => 'submit',
			'options' => array(
				'value' => '<?= (!empty($data->submitLabel)) ? $data->submitLabel : 'Submit' ?>'

			),
			'attributes' => array(
				'class' => 'btn btn-success'
			)
		));
	}

	public function getFilters() {
		return array(
		<?php
		foreach (explode(',', $data->elements) as $element):
			if (empty($element))
				continue;

			$element = \Util::_toCamel($element)
			?>

			'<?= $element ?>' => array(
				'required' => true,
			),
		<?php endforeach; ?>

		);
	}
}
		<?php
		if (!is_dir(MODULES . $module . DIRECTORY_SEPARATOR . 'Forms'))
			mkdir(MODULES . $module . DIRECTORY_SEPARATOR . 'Forms');

		if (isset($data->overWrite) || (!isset($data->overWrite) && !is_readable(MODULES . $module . DIRECTORY_SEPARATOR . 'Forms' . DIRECTORY_SEPARATOR . $data->name . '.php')))
			return (file_put_contents(MODULES . $module . DIRECTORY_SEPARATOR . 'Forms' . DIRECTORY_SEPARATOR . $data->name . '.php', '<' . '?php' . "\n" . ob_get_clean()));
		return true;
	}

}
