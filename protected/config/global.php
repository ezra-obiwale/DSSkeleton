<?php

return array(
    'server' => 'production',
    'app' => array(
        'name' => 'DScribe Skeleton',
        'domain' => 'http://dscribe.ezraobiwale.com',
        'webmaster' => 'dscribe@ezraobiwale.com'
    ),
    'modules' => array(
        'App' => array(
            'alias' => 'site',
            'defaults' => array(
                'controller' => 'Index',
                'action' => 'index',
            ),
        ),
    ),
    'defaults' => array(
        'module' => 'App',
        'theme' => 'DScribe',
        'defaultLayout' => 'guest',
        'errorLayout' => 'error',
    ),
    'db' => array(
        'development' => array(
            'dsn' => 'mysql:host=localhost;dbname=ds_db',
            'user' => '',
            'password' => '',
            'options' => array(
                'tablePrefix' => 'ds_',
                'create' => true
            )
        ),
        'production' => array(
        ),
    ),
);
