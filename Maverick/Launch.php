<?php

namespace Maverick;

session_start();

define('DS',               DIRECTORY_SEPARATOR);
define('PHP_EXT',          '.php');

define('ROOT_PATH',        dirname(__DIR__) . DS);
define('MAVERICK_PATH',    __DIR__          . DS);
define('APPLICATION_PATH', ROOT_PATH        . 'Application' . DS);
define('PUBLIC_PATH',      ROOT_PATH        . 'Public'      . DS);

/**
 * Returns the singleton instance of \Maverick\Maverick
 *
 * @return \Maverick\Maverick
 */
function Maverick() {
    return Maverick::getInstance();
}

// Requires to file with the debug functions
require_once(MAVERICK_PATH . 'Debug.php');

// Checks for the composer autoloader.
// If it exists, it requires it.
$composerAutoloader = ROOT_PATH . 'vendor' . DS . 'autoload.php';

if(file_exists($composerAutoloader)) {
    require_once($composerAutoloader);
}

require(MAVERICK_PATH . 'Maverick' . PHP_EXT);