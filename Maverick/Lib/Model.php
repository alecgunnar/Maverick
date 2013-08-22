<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Model {
    /**
     * Holds all of the data for the model
     *
     * @var array $data
     */
    protected $data = array();

    /**
     * Holds all of the original data for the model
     *
     *
     * @var array $data_orig
     */
    protected $data_orig = array();

    /**
     * Sets the data for the model
     *
     * @param array $data=array()
     */
    public function __construct($data=array()) {
        $this->set($data);
    }

    /**
     * Adds to the model after it is initially created
     *
     * @param  string | array $name
     * @param  string $value
     * @return null
     */
    public function set($name, $value='') {
        if(is_array($name) && count($name)) {
            foreach($name as $k => $v) {
                if(is_array($v)) {
                    $value = new self($v);
                } else {
                    $value = $v;
                }

                $this->data[$k] = $value;
            }

            $this->data_orig = $name;
        } elseif(!is_array($name)) {
            $this->data_orig[$name] = $value;

            if(is_array($value)) {
                $value = new self($value);
            }

            $this->data[$name] = $value;
        }
    }

    /**
     * Gets any requested value
     *
     * @param  string  $key
     * @param  boolean $asArray=false
     * @return \Maverick\Model | string | null
     */
    public function get($key, $asArray=false) {
        if($asArray) {
            return $this->getAsArray($key);
        }

        if(array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Get the original value, not an object of this class
     *
     * @param  string $key
     * @return string \ array
     */
    public function getAsArray($key) {
        if(array_key_exists($key, $this->data_orig)) {
            return $this->data_orig[$key];
        }

        return null;
    }
}