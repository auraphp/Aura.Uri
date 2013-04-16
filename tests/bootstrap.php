<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('AURA_URI_TEST_ROOT', __DIR__);

// preload source files
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src.php';

// autoload test files
spl_autoload_register(function($class) {
    $file = dirname(__DIR__). DIRECTORY_SEPARATOR
          . 'tests' . DIRECTORY_SEPARATOR
          . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
