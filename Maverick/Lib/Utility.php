<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Utility {
    /**
     * Generates a string of random letters and numbers
     *
     * @param  integer $length
     * @return string
     */
    public static function generateToken($length) {
        $characters = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890';
        $token      = '';
        $l          = 0;

        while($l < $length) {
            $token .= substr($characters, rand(0, strlen($characters)), 1);

            $l++;
        }

        return $token;
    }
}