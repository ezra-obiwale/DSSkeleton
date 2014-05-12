<?php
/*
 */

namespace DsUtil\Services;

use DScribe\Core\AService,
	DScribe\Core\Engine,
	Util;

/**
 * Description of ModulesService
 *
 * @author topman
 */
class ModulesService extends AService {

	protected $moduleForm;

	protected function inject() {
		return array(
			'moduleForm' => array(
				'class' => 'DsUtil\Forms\ModuleForm'
			)
		);
	}

	public function getModuleForm() {
		return $this->moduleForm;
	}

	private function getAllModules() {
		return Util::readDir(MODULES, Util::DIRS_ONLY, false, null, true);
	}

	public function getModules() {
		$allModules = $this->getAllModules();

		sort($allModules);
		$activeModules = array_keys(Engine::getConfig('modules'));
		$modules = array();
		foreach ($allModules as $module) {
			$modules[$module] = array(
				'status' => (in_array($module, $activeModules)),
				'default' => (Engine::getDefaultModule() === $module),
			);
		}
		return $modules;
	}

	public function checkExists($module) {
		if (strtolower($module) === 'dsutil') {
			throw new \Exception('Access denied to module "DsUtil"');
		}

		if (!in_array($module, $this->getAllModules()))
			throw new \Exception('Module "' . $module . '" does not exist');
	}

	public function activateModule($module) {
		$this->checkExists($module);

		$config = Engine::getConfig();
		$config['modules'][$module] = array();
		$content = str_replace("=> \n", '=>', var_export($config, true));
		return (file_put_contents(CONFIG . 'global.php', '<' . '?php' . "\r\n\treturn " . $content . ';'));
	}

	public function deactivateModule($module) {
		$this->checkExists($module);

		$config = Engine::getConfig();
		unset($config['modules'][$module]);
		$content = str_replace("=> \n", '=>', var_export($config, true));
		return (file_put_contents(CONFIG . 'global.php', '<' . '?php' . "\r\n\treturn " . $content . ';'));
	}

	public function createModule(\Object $data) {
		if (@mkdir(MODULES . ucfirst($data->name), 0755)) {
			if (isset($data->activate)) {
				$this->activateModule($data->name);
			}
			return true;
		}

		return false;
	}

	public function makeDefault($module) {
		$this->checkExists($module);

		$config = Engine::getConfig();
		if (!in_array($module, array_keys($config['modules'])))
			return false;
		$config['defaults']['module'] = $module;

		$content = str_replace(array("=> \n", '=>  '), '=> ', var_export($config, true));
		return file_put_contents(CONFIG . 'global.php', '<' . '?php' . "\n\treturn " . $content . ';');
	}

}
