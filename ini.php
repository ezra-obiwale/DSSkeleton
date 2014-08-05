<?php

/**
 * All constants are required for effective running of your app.
 * 
 * DO NOT COMMENT OUT
 */
// IMPORTANT - DO NOT CHANGE
define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('APP', ROOT . 'protected' . DIRECTORY_SEPARATOR);
define('VENDOR', ROOT . 'vendor' . DIRECTORY_SEPARATOR);
define('MODULES', APP . 'modules' . DIRECTORY_SEPARATOR);
define('CONFIG', APP . 'config' . DIRECTORY_SEPARATOR);

// YOU MAY CHANGE
define('THEMES', APP . 'themes' . DIRECTORY_SEPARATOR);
define('DATA', APP . 'data' . DIRECTORY_SEPARATOR);
define('CACHE', DATA . 'cache' . DIRECTORY_SEPARATOR);

define('ENGINE', 'DScribe\Core\Engine');
