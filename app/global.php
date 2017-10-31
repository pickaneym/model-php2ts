<?php

ini_set('display_errors', 1);
ini_set('xdebug.max_nesting_level', 3000);

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__));
define('PHP_MODELS_DIR', ROOT_PATH . DS . 'tmp' . DS . 'model');
define('TS_MODELS_DIR', ROOT_PATH . DS . 'tmp' . DS . 'ts-models');

function camel2dashed($fileName){
    return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $fileName));
}
