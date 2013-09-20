<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 *
 * Please note and be aware of:
 * This is a simple email address validator,
 * it will not validate all email addresses.
 *
 */

namespace Maverick\Lib;

class Validator_IsEmail extends Validator {
    /**
     * The error message for an empty field
     *
     * @var string
     */
    public $errorMessage = 'You must enter an email address';

    /**
     * A list of all of the ASCII characters
     * which are allowed in the local name
     *
     * @var string
     */
    private $allowedLocal = "!#$%&'*+-/=?^_`{|}~.";

    /**
     * A list of all of the ASCII characters
     * which are allowed in the host name
     *
     * @var string
     */
    private $allowedRemote = "-.";

    /**
     * Validates the field
     *
     * @return boolean
     */
    public function isValid() {
        if(!$this->value) {
            return true;
        }

        $explodeAt = explode('@', $this->value);

        if(count($explodeAt) != 2) {
            return false;
        }

        if($this->checkLocal($explodeAt[0]) && $this->checkRemote($explodeAt[1])) {
            return true;
        }

        return false;
    }

    /**
     * Check the local portion of the email address
     *
     * @param  string $localName
     * @return boolean
     */
    private function checkLocal($localName) {
        if($this->checkName($localName, $this->allowedLocal)) {
            return true;
        }

        return false;
    }

    /**
     * Check the remote portion of the email address
     *
     * @param  string $hostName
     * @return boolean
     */
    private function checkRemote($hostName) {
        $chars = str_split($hostName);
        $last  = count($chars) - 1;

        if($chars[0] == '-' || $chars[0] == '.' || $chars[$last] == '-' || $chars[$last] == '.') {
            return false;
        }

        if($this->checkName($hostName, $this->allowedLocal)) {
            return true;
        }
    }

    /**
     * Checks the individual characters of the name
     *
     * @param  string $name
     * @param  string $acceptedCharacters
     * @return boolean
     */
    private function checkName($name, $acceptedCharacters) {
        $chars = str_split($name);

        foreach($chars as $c) {
            if(!ctype_alnum($c) && strpos($acceptedCharacters, $c) === false) {
                return false;
            }
        }

        return true;
    }
}