<?php
/*
 */

namespace DsUtil\Services;

use DScribe\Core\Engine,
	Object,
	Util;

/**
 * Description of ControllersService
 *
 * @author topman
 */
class ControllersService extends SuperService {

	protected $form;

	protected function inject() {
		return array_merge(parent::inject(), array(
				'form' => array(
					'class' => 'DsUtil\Forms\ControllerForm'
				),
		));
	}

	public function getForm($module) {
		$this->loadFormLayouts($module);
		$this->loadFormControllers();
		$this->form->doConstruct();
		return $this->form;
	}

	/**
	 *
	 * @param string $module
	 */
	protected function parseFormControllers() {
		$allControllers = $this->getAll('controllers');
		return $this->parseForForm($allControllers);
	}

	private function loadFormControllers() {
		$this->form->setControllers($this->parseFormControllers());
	}

	private function loadFormLayouts($module) {
		$layouts = array();
		$defTheme = Engine::getConfig('defaults', 'theme', false);
		if (is_dir(THEMES . $defTheme . DIRECTORY_SEPARATOR . 'layouts')) {
			foreach (\Util::readDir(THEMES . $defTheme . DIRECTORY_SEPARATOR . 'layouts', \Util::FILES_ONLY) as $path) {
				$info = \Util::pathInfo($path);
				$layouts[$info['filename']] = $info['filename'] . ' (T)';
			}
		}

		if (is_dir(MODULES . $module . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layouts')) {
			foreach (\Util::readDir(MODULES . $module . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layouts', \Util::FILES_ONLY) as $path) {
				$info = \Util::pathInfo($path);
				$layouts[$info['filename']] = $info['filename'] . ' (M)';
			}
		}

		$this->form->setLayouts($layouts);
	}

	public function getModulesService() {
		return $this->modulesService;
	}

	public function parseAllControllers($module) {
		$controllers = array();
		foreach ($this->getAll('controllers', $module) as $controller) {
			$class = ucfirst($module) . '\Controllers\\' . $controller;
			if (!class_exists($class)) {
				$controllers[$controller] = array();
				continue;
			}

			$class = $this->loadClass($class);
			$controllers[$controller]['actions'] = ($class) ? $class->getActions() : array();

			$config = Engine::getConfig();
			if (@$config['defaults']['module'] === $module && isset($config['modules'][$module]['defaults'])) {
				$controllers[$controller]['defaults'] = @$config['modules'][$module]['defaults'];
			}
		}

		return $controllers;
	}

	public function createController($module, Object $data, $withViews = true) {
		$actions = array();
		if (isset($data->autoGen))
			$data->actions = 'index,new,edit,view,delete';

		if (!empty($data->actions))
			$actions = explode(',', $data->actions);

		$extend = (!empty($data->extend)) ? $data->extend : 'DScribe\Core\AController';
		$extendInfo = pathinfo(str_replace('\\','/', $extend));

		ob_start();
		?>

namespace <?= ucfirst($module) ?>\Controllers;
<?php if (ucfirst($module) . '/Controllers' !== $extendInfo['dirname']): ?>

use <?= $extend ?>;
<?php endif; ?>

class <?= ucfirst($data->name) ?>Controller extends <?= $extendInfo['filename'] ?> {
        <?php $service = ucfirst($module) . '\Services\\' . ucfirst($data->name) . 'Service';
        if (class_exists($service)) :
            ?>

        /*
         * @var \<?= $service ?>
         */
         protected $service;
        <?php endif; ?>
<?php if (!empty($data->layout)): ?>

	public function init() {
		$this->layout = '<?= $data->layout ?>';
	}
<?php endif; ?>
<?php
if (!empty($data->noCache)):
	if ($data->noCache[strlen($data->noCache) - 1] === ',')
		$data->noCache[strlen($data->noCache) - 1] = '';
	$noCache = str_replace(',', "', '", $data->noCache);
	?>

	public function noCache() {
		return array('<?= $noCache ?>');
	}
<?php endif; ?>
<?php foreach ($actions as $action): if (empty($action)) continue; ?>

	public function <?= Util::_toCamel($action) ?>Action(<?= (isset($data->autoGen) && in_array($action, array('edit', 'view', 'delete'))) ? '$id' : '' ?><?= ($action === 'delete') ? ', $confirm = null' : '' ?>) {
	<?php
	if (isset($data->autoGen)) {
		switch ($action) {
			case 'index':
				echo $this->createIndexAction();
				break;
			case 'new':
				echo $this->createNewAction($module, $data);
				break;
			case 'edit':
				echo $this->createEditAction($module, $data);
				break;
			case 'view':
				echo $this->createViewAction();
				break;
			case 'delete':
				echo $this->createDeleteAction($module, $data);
				break;
		}
	}
	?>

	}
<?php endforeach; ?>

}
		<?php
		if (!is_dir(MODULES . $module . DIRECTORY_SEPARATOR . 'Controllers'))
			mkdir(MODULES . $module . DIRECTORY_SEPARATOR . 'Controllers');

		$content = ob_get_clean();
		if ($withViews)
			$this->createActionViews($module, $data, $actions);

		try {
			if (isset($data->overWrite) || (!isset($data->overWrite) && !is_readable(MODULES . $module . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $data->name . 'Controller.php')))
				return (file_put_contents(MODULES . $module . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $data->name . 'Controller.php', '<' . '?php' . "\n" . $content));
			return true;
		} catch (\Exception $ex) {
			throw new \Exception($ex->getMessage());
		}
	}

	private function createIndexAction() {
		ob_start();
		?>

		return $this->view->variables(array(
			'models' => $this->service->fetchAll(),
		));
		<?php
		return ob_get_clean();
	}

	private function createIndexView($module, $data) {
		$modelClass = $module . '\Models\\' . $data->name;
		$class = new $modelClass;
		$properties = array_keys($class->toArray(true));
		$headers = "";
		$columns = "";
		for ($i = 0; $i < count($properties); $i++) {
			if ($i === 4)
				break;

			$headers .= "'" . ucwords(str_replace('_', ' ', $properties[$i])) . "',";
			$columns .= "\n\t\t\t\t" . 'Table::addRowData($model->get' . ucfirst(\Util::_toCamel($properties[$i])) . '());';
		}

		$headers = substr($headers, 0, strlen($headers) - 1);
		ob_start();
		?>
<\?php
Table::init(array('class' => 'table table-striped'));
Table::setHeaders(array(<?= $headers ?>, ''));

foreach ($models as $model) {
	$viewBtn = '<a title="view details" href="' . $this->url('<?= \Util::camelToHyphen($module) ?>', '<?= \Util::camelToHyphen($data->name) ?>', 'view', array($model->getId())) . '" class="btn btn-mini btn-success"><i class="icon-folder-open"></i></a>';
	$editBtn = '<a title="edit" href="' . $this->url('<?= \Util::camelToHyphen($module) ?>', '<?= \Util::camelToHyphen($data->name) ?>', 'edit', array($model->getId())) . '" class="btn btn-mini btn-info"><i class="icon-edit"></i></a>';
	$deleteBtn = '<a title="delete" href="' . $this->url('<?= \Util::camelToHyphen($module) ?>', '<?= \Util::camelToHyphen($data->name) ?>', 'delete', array($model->getId())) . '" class="btn btn-mini btn-danger"><i class="icon-trash"></i></a>';

	Table::newRow();
	<?= $columns ?>

	Table::addRowData($viewBtn . ' ' . $editBtn . ' ' . $deleteBtn, array('width' => '100px');
}
?>
<div class="row-fluid">
	<div class="span12">
		<h1>
			<small class="text-error"><?= ucwords(str_replace('_', ' ', \Util::camelTo_($data->name))) ?>s</small>
			<a href="<\?= $this->url('<?= \Util::camelToHyphen($module) ?>', '<?= \Util::camelToHyphen($data->name) ?>', 'new') ?>" class="btn btn-success pull-right">New <?= ucfirst($data->name) ?></a>
		</h1>
		<hr />
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<\?= Table::render() ?>
	</div>
</div>
		<?php
		return ob_get_clean();
	}

	private function createNewAction($module, $data) {
		ob_start();
		?>

		$form = $this->service->getForm();
		if ($this->request->isPost()) {
			$form->setData($this->request->getPost());
			if ($form->isValid() && $this->service->create($form->getModel())) {
				$this->flash()->setSuccessMessage('Save successful');
				$this->redirect('<?= \Util::camelToHyphen($module) ?>', '<?= \Util::camelToHyphen($data->name) ?>', 'index');
			} else {
				$this->flash()->setErrorMessage('Save failed');
			}
		}
		return $this->view->variables(array(
			'form' => $form,
		));

		<?php
		return ob_get_clean();
	}

	private function createNewView($module, $data) {
		ob_start();
		?>
<div class="row-fluid">
	<div class="span12">
		<h1><small class="text-error">New <?= ucwords(str_replace('_', ' ', \Util::camelTo_($data->name))) ?></small></h1>
		<hr />
	</div>
</div>
<div class="row-fluid">
	<div class="span8 offset1">
		<\?= $form->setAttribute('action', $this->currentPath())->render() ?>
	</div>
</div>
<script>
    $(function(){
        $('form#<\?= $form->getName() ?> input[type="submit"]').parent().append('<input class="btn btn-primary" type="submit" name="saveAndNew" value="Save and Add New" />');        
    });
</script>
		<?php
		return ob_get_clean();
	}

	private function createEditAction($module, $data) {
		ob_start();
		?>

		$model = $this->service->findOne($id);
		$form = $this->service->getForm();
		$form->setModel($model);
		if ($this->request->isPost()) {
			$form->setData($this->request->getPost());
			if ($form->isValid() && $this->service->save($form->getModel())) {
				$this->flash()->setSuccessMessage('Save successful');
				$this->redirect('<?= \Util::camelToHyphen($module) ?>', '<?= \Util::camelToHyphen($data->name) ?>', 'index');
			} else {
				$this->flash()->setErrorMessage('Save failed');
			}
		}
		return $this->view->variables(array(
			'form' => $form,
		));
		<?php
		return ob_get_clean();
	}

	private function createEditView($module, $data) {
		ob_start();
		?>
<div class="row-fluid">
	<div class="span12">
		<h1><small class="text-error">Edit <?= ucwords(str_replace('_', ' ', \Util::camelTo_($data->name))) ?></small></h1>
		<hr />
	</div>
</div>
<div class="row-fluid">
	<div class="span8 offset1">
		<\?= $form->setAttribute('action', $this->currentPath())->render() ?>
	</div>
</div>
		<?php
		return ob_get_clean();
	}

	private function createViewAction() {
		ob_start()
		?>

		return $this->view->variables(array(
			'model' => $this->service->findOne($id),
		));
		<?php
		return ob_get_clean();
	}

	private function createViewView($module, $data) {
		$modelClass = $module . '\Models\\' . $data->name;
		$class = new $modelClass;
		$properties = array_keys($class->toArray(true));
		ob_start();
		?>
<div class="row-fluid">
	<div class="span12">
		<h1><small class="text-error"><?= ucwords(str_replace('_', ' ', \Util::camelTo_($data->name))) ?> View</small></h1>
		<hr />
	</div>
</div>
<div class="row-fluid">
	<div class="span8 offset1">
            <\?php
                foreach ($model->toArray() as $column => $value) {
                    if ($column === 'id')
                        continue;
                    ?>
                    <div class="row-fluid">
                        <div class="span3">
                            <\?= ucwords(str_replace('_', ' ', $column)) ?>
                        </div>
                        <div class="span9">
                            <\?= $val ?>
                        </div>
                    </div>
                    <\?php
                }
            ?>
	</div>
</div>
		<?php
		return ob_get_clean();
	}

	private function createDeleteAction($module, $data) {
		ob_start();
		?>

		$model = $this->service->findOne($id);
		if ($confirm == 1) {
			if ($this->service->delete()) {
				$this->flash()->setSuccessMessage('Delete successful');
			} else {
				$this->flash()->setErrorMessage('Delete failed');
			}
			$this->redirect('<?= \Util::camelToHyphen($module) ?>', '<?= \Util::camelToHyphen($data->name) ?>', 'index');
		}

		return $this->view->variables(array(
			'model' => $model,
		));
		<?php
		return ob_get_clean();
	}

	private function createDeleteView($module, $data) {
		ob_start();
		?>
<div class="row-fluid">
	<div class="span12">
		<h1><small class="text-error">Delete <?= ucwords(str_replace('_', ' ', \Util::camelTo_($data->name))) ?></small></h1>
		<hr />
	</div>
</div>
<div class="row-fluid">
	<div class="span8 offset1">
		<p>Are you sure you want to delete?</p>
		<p class="form-actions">
			<a href="<\?= $this->url('<?= \Util::camelToHyphen($module) ?>','<?= \Util::camelToHyphen($data->name) ?>','delete', array('id' => $model->getId(), 'confirm' => 1)) ?>" class="btn btn-danger">Delete</a>
			<a href="<\?= $this->url('<?= \Util::camelToHyphen($module) ?>','<?= \Util::camelToHyphen($data->name) ?>','index') ?>" class="btn">Cancel</a>
		</p>
	</div>
</div>
		<?php
		return ob_get_clean();
	}

	public function createActionViews($module, $data, array $actions) {
		try {
			$path = MODULES . $module . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . \Util::camelToHyphen($data->name);
			if (!is_dir($path))
				mkdir($path, 0777, true);

			foreach ($actions as $action) {
				ob_start();
				if (isset($data->autoGen)) {
					switch ($action) {
						case 'index':
							echo $this->createIndexView($module, $data);
							break;
						case 'new':
							echo $this->createNewView($module, $data);
							break;
						case 'edit':
							echo $this->createEditView($module, $data);
							break;
						case 'view':
							echo $this->createViewView($module, $data);
							break;
						case 'delete':
							echo $this->createDeleteView($module, $data);
							break;
					}
				}

				if (isset($data->overWrite) || (!isset($data->overWrite) && !is_readable($path . DIRECTORY_SEPARATOR . \Util::camelToHyphen($action) . '.phtml')))
					file_put_contents($path . DIRECTORY_SEPARATOR . \Util::camelToHyphen($action) . '.phtml', str_replace('<\?', '<' . '?', ob_get_clean()));
			}
			return true;
		} catch (\Exception $ex) {
			throw new \Exception($ex->getMessage());
		}
	}

	private function checkExists($module, $controller) {
		if (!class_exists($module . '\Controllers\\' . $controller))
			throw new \Exception('Controller "' . $controller . '" does not exist');

		return true;
	}

	public function makeDefaultController($module, $controller) {
		$this->modulesService->checkExists($module);
		$this->checkExists($module, $controller);

		$config = Engine::getConfig();
		$config['modules'][$module]['defaults']['controller'] = substr($controller, 0, strlen($controller) - 10);
		unset($config['modules'][$module]['defaults']['action']);

		$content = str_replace(array("=> \n", '=>  '), '=> ', var_export($config, true));
		return file_put_contents(CONFIG . 'global.php', '<' . '?php' . "\n\treturn " . $content . ';');
	}

	public function makeDefaultAction($module, $action) {
		$this->modulesService->checkExists($module);

		$config = Engine::getConfig();
		$config['modules'][$module]['defaults']['action'] = $action;

		$content = str_replace(array("=> \n", '=>  '), '=> ', var_export($config, true));
		return file_put_contents(CONFIG . 'global.php', '<' . '?php' . "\n\treturn " . $content . ';');
	}

}
