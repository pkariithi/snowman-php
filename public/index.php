<?php

// measure script execution time
ini_set('precision', 16);
define('START_TIME', microtime(true));

// environment - dev / uat / prod
define('ENV', 'dev');

// path variables
define('DS', DIRECTORY_SEPARATOR);
define('EOL', PHP_EOL);

// define ROOT and SRC_DIR
// You can change the src_dir path as necessary
define('ROOT', dirname(__FILE__, 2).DS);
define('SRC_DIR', ROOT.'src'.DS);

// composer
require SRC_DIR.'vendor'.DS.'autoload.php';