<?php

error_reporting(E_ALL | E_ERROR);

define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('APP', ROOT . 'protected' . DIRECTORY_SEPARATOR);
define('VENDOR', ROOT . 'vendor' . DIRECTORY_SEPARATOR);
define('MODULES', APP . 'modules' . DIRECTORY_SEPARATOR);
define('THEMES', APP . 'themes' . DIRECTORY_SEPARATOR);
define('CONFIG', APP . 'config' . DIRECTORY_SEPARATOR);
define('DATA', APP . 'data' . DIRECTORY_SEPARATOR);
define('CACHE', DATA . 'cache' . DIRECTORY_SEPARATOR);

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
if (@strtolower(@$config['server']) == 'development')
    ini_set('display_errors', 1);
else
    ini_set('display_errors', 0);

function exceptionHandler(\Exception $ex) {
    $cEx = new \DScribe\Core\Exception($ex->getMessage(), false, false);
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
