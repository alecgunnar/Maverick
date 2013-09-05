<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Autoloader {
    /**
     * The autoloader
     *
     * @throws \Exception
     * @param  string $className
     * @return boolean
     */
    public static function autoload($className) {
        $expClassName = explode('\\', $className);
        $vendor       = $expClassName[0];

        array_shift($expClassName);

        switch($vendor) {
            case "Maverick":
                return self::maverick($expClassName);
            default:
                return self::psr0($expClassName, $vendor);
        }
    }

    /**
     * Looks for classes with Maverick as the vendor
     *
     * @param  array   $className
     * @param  boolean $checkForExtension
     * @return boolean
     */
    private static function maverick($className) {
        $extensionName = $className;
    
        array_unshift($extensionName, 'Extension');

        if(self::psr0($extensionName, 'Application')) {
            return true;
        }

        return self::psr0($className, '', MAVERICK_PATH);
    }

    /**
     * Autoloads classes from the root directory
     *
     * @param  array  $className
     * @param  string $vendor
     * @param  string $path=''
     * @return boolean
     */
    private static function psr0($className, $vendor, $path='') {
        $count = count($className);
        $path  = $path ?: ROOT_PATH;
        $load  = $path . $vendor;

        if($load[strlen($load) - 1] == DS) {
            $load = substr($load, 0, -1);
        }

        for($i = 0; $i < $count; $i++) {
            $load .= DS;

            if(($i + 1) == $count) {
                $load .= str_replace('_', DS, $className[$i]);
            } else {
                $load .= $className[$i];
            }
        }

        $load .= PHP_EXT;

        return self::loadFile($load);
    }

    /**
     * Checks to see if a file exists, then loads it if it does.
     *
     * @param  string $file
     * @return boolean
     */
    private static function loadFile($file) {
        if(file_exists($file)) {
            require_once $file;

            return true;
        }

        return false;
    }
}