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
                break;
            default:
                return self::psr0($vendor, $expClassName);
                break;
        }
    }

    /**
     * Looks for classes with Maverick as the vendor
     *
     * @param  array $cn
     * @return boolean
     */
    private static function maverick($cn) {
        $en = $cn;

        array_unshift($en, 'Extension');

        if(self::psr0('Application', $en)) {
            return true;
        }

        return self::psr0('Maverick', $cn);
    }

    /**
     * Autoloads classes from the root directory
     *
     * @param  string $vendor
     * @param  array  $cn
     * @return boolean
     */
    private static function psr0($vendor, $cn) {
        $ct   = count($cn);
        $load = ROOT_PATH . $vendor;

        for($i = 0; $i < $ct; $i++) {
            $load .= DS;

            if(($i + 1) == $ct) {
                $load .= str_replace('_', DS, $cn[$i]);
            } else {
                $load .= $cn[$i];
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