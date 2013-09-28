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

                $this->{$k} = $value;
            }

            $this->data = $name;
        } elseif(!is_array($name)) {
            $this->data[$name] = $value;

            if(is_array($value)) {
                $value = new self($value);
            }

            $this->{$name} = $value;
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

        if(isset($this->{$key})) {
            return $this->{$key};
        }

        return null;
    }

    /**
     * Get the original value, not an object of this class
     *
     * @param  string $key=''
     * @return string \ array
     */
    public function getAsArray($key='') {
        if($key && array_key_exists($key, $this->data)) {
            return $this->data[$key];
        } else {
            return $this->data;
        }

        return null;
    }
}