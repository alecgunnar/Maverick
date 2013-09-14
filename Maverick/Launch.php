<?php

namespace Maverick;

session_start();

if(!defined('DS'))               define('DS',               DIRECTORY_SEPARATOR);
if(!defined('PHP_EXT'))          define('PHP_EXT',          '.php');

if(!defined('ROOT_PATH'))        define('ROOT_PATH',        dirname(__DIR__) . DS);
if(!defined('MAVERICK_PATH'))    define('MAVERICK_PATH',    __DIR__          . DS);
if(!defined('APPLICATION_PATH')) define('APPLICATION_PATH', ROOT_PATH        . 'Application' . DS);
if(!defined('PUBLIC_PATH'))      define('PUBLIC_PATH',      ROOT_PATH        . 'Public'      . DS);

// Gets the debug functions
include_once(MAVERICK_PATH . 'Debug' . PHP_EXT);

// Gets the composer autoloader, if it exists
$composerAutoloader = ROOT_PATH . 'vendor' . DS . 'autoload' . PHP_EXT;

if(file_exists($composerAutoloader)) {
    require_once($composerAutoloader);
}

// Loads Maverick
require(MAVERICK_PATH . 'Maverick' . PHP_EXT);