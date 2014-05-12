<?php
/*
 */

namespace DsUtil\Services;

use DScribe\Core\AService,
	Util;

/**
 * Description of SuperService
 *
 * @author topman
 */
class SuperService extends AService {

	protected $modulesService;

	protected function inject() {
		return array(
			'modulesService' => array(
				'class' => 'DsUtil\Services\ModulesService'
			),
		);
	}

	/**
	 * Fetches available models
	 * @param string|null $module
	 * @return array
	 */
	public function getAll($type, $module = null) {
		$type = ucfirst($type);

		if ($module !== null) {
			$this->modulesService->checkExists($module);
			if (!is_dir(MODULES . $module . DIRECTORY_SEPARATOR . $type))
				return array();
		}
		$return = array();
		foreach (Util::readDir(MODULES, Util::FILES_ONLY, true, '.php') as $path) {
			$this->parseFoundFiles($return, \Util::getFileName($path), $path, $type, $module);
		}
		if ($module !== null)
			sort($return);
		return $return;
	}

	private function parseFoundFiles(&$return, $fileName, $path, $type, $module) {
		if (is_array($path)) {
			foreach ($path as $pathh) {
				$this->parseFoundFiles($return, $fileName, $pathh, $type, $module);
			}
			return true;
		}

		$nm = str_replace(array(MODULES, '/', '.php'), array('', '\\'), $path);

		if ($module !== null && substr($nm, 0, strlen($module)) !== $module) {
			return false;
		}
		if (!strstr($nm, '\\' . $type . '\\'))
			return false;

		if (strstr($nm, '\\' . $type . '\\', true) === 'DsUtil')
			return false;

		$info = pathinfo($fileName);

		if ($module === null) {
			$return[$nm] = $info['filename'];
		} else {
			$return[] = $info['filename'];
		}
	}

	/**
	 * Creates an array that can be given a form select element
	 * @param string|array $all If string, all class types of the $all will be parsed.
	 * If array, it will just be parsed
	 * @return array
	 */
	protected function parseForForm($all) {
		$all = (is_array($all)) ? $all : $this->getAll($all);
		$return = array();
		foreach ($all as $nm => $class) {
			$return[strstr($nm, '\\', true)][$class] = $nm;
		}
		return $return;
	}

	protected function loadClass($className, $returnReflection = false) {
		$refClass = new \ReflectionClass($className);
		if ($refClass->isAbstract())
			return null;
		else
			return ($returnReflection) ? $refClass : $refClass->newInstanceArgs(array(false));
	}

}
