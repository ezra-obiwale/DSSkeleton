<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_WARNING);

include_once 'ini.php';
ini_set('display_errors', 1);

/**
 * Execute a method of the defined engine
 * @param string $methodName
 * @param mixed $_ Parameters for the method
 * @return mixed
 */
function engine($methodName, $_ = null) {
    $params = func_get_args();
    unset($params[0]);
    return call_user_func_array(array(ENGINE, $methodName), $params);
}

/**
 * Execute a get method of the defined engine
 * @param string $methodName Method name with the "get" prefix
 * @param mixed $_ Parameters for the method
 * @return mixed
 */
function engineGet($methodName, $_ = null) {
    $params = func_get_args();
    unset($params[0]);
    return call_user_func_array(array(ENGINE, 'get' . $methodName), $params);
}

// load overall config
$config = require CONFIG . 'global.php';

$moduleAutoload = array();

// load module level config and autoloads
foreach ($config['modules'] as $module => &$conf) {
    $moduleConfig = MODULES . $module . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'local.php';
    if (is_array($conf) && is_readable($moduleConfig)) {
        $moduleConfig = include $moduleConfig;
        $conf = array_merge($moduleConfig, $conf);

        if (isset($moduleConfig['autoload']))
            $moduleAutoload[$module] = $moduleConfig['autoload'];
    }
}

// show | hide errors based on server type
if (@$config['displayErrors'] || @strtolower(@$config['server']) == 'development')
    ini_set('display_errors', 1);
else
    ini_set('display_errors', 0);

function exceptionHandler(\Exception $ex) {
    $cEx = new \dScribe\Core\Exception($ex->getMessage(), false, false);
    $cEx->specifics($ex->getFile(), $ex->getLine());
    $cEx->push();
    exit;
}

set_exception_handler('exceptionHandler');

function errorHandler($code, $msg, $file, $line) {
    exceptionHandler(new \ErrorException($msg, $code, null, $file, $line));
}

set_error_handler('errorHandler', E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR | E_PARSE | E_RECOVERABLE_ERROR | E_USER_ERROR);

require_once 'autoloader.php';
