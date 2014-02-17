<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Model_Uri {
    /**
     * The original uri
     *
     * @var string
     */
    private $uri = '';

    /**
     * https yes? or no?
     *
     * @var boolean
     */
    protected $https = false;

    /**
     * The host name of the url
     *
     * @var string
     */
    protected $hostName = '';

    /**
     * The path to the resource
     *
     * @var string
     */
    protected $path = '';

    /**
     * The series query data of the url
     *
     * @var array
     */
    protected $queryData = array();

    /**
     * The constructor
     *
     * @param boolean $https=false
     * @param string  $domain=''
     * @param string  $path=''
     * @param array   $queryData=array()
     */
    public function __construct($uri='', $https=false, $hostName='', $path='', $queryData=array()) {
        if(!is_bool($https)) {
            throw new \Maverick\Exception\InvalidParameterException('"\Maverick\Lib\Builder_Url::__construct" expects parameter #2 to be a boolean value.');
        }

        $this->uri      = $uri;
        $this->https    = $https;
        $this->hostName = $hostName;

        $this->setPath($path);

        if(is_array($queryData) && count($queryData)) {
            $this->setQueryData($queryData);
        }
    }

    /**
     * Sets the uri
     *
     * @param  string $uri
     * @return self
     */
    public function setUri($uri) {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Gets the uri
     *
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Sets the whether this url is secure or not
     *
     * @param  boolean $https=true
     * @return self
     */
    public function setHttps($https=true) {
        if(!is_bool($https)) {
            throw new \Maverick\Exception\InvalidParameterException('"\Maverick\Lib\Builder_Url::__construct" expects parameter #1 to be a boolean.');
        }

        $this->https = $https;

        return $this;
    }

    /**
     * Gets the value of $this->https
     *
     * @return boolean
     */
    public function getHttps() {
        return $this->https;
    }

    /**
     * Sets the host name of the url
     *
     * @param  string $hostName
     * @return self
     */
    public function setHostName($hostName) {
        $this->hostName = $hostName;

        return $this;
    }

    /**
     * Gets the host name
     *
     * @return string
     */
    public function getHostName() {
        return $this->hostName;
    }

    /**
     * Sets the path to the resource of the url
     *
     * @param  string $path
     * @return self
     */
    public function setPath($path) {
        $this->path = trim($path, '/');

        return $this;
    }

    /**
     * Gets the path to the resource
     *
     * return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Add query data to the url
     *
     * @param  string|array $label
     * @param  string|null  $value
     * @return self
     */
    public function setQueryData($data, $value=null) {
        $callable = function(&$value) {$value = urlencode($value);};

        if(is_array($data)) {
            array_walk($data, $callable);
            $this->queryData = array_merge($this->queryData, $data);
        } else {
            if(!is_string($value)) {
                throw new \Maverick\Exception\InvalidParameterException('"addQueryData" expects parameter #2 to be a string.');
            }

            $callable($value);

            $this->queryData[$data] = $value;
        }

        return $this;
    }

    /**
     * Gets the query data
     *
     * @return array
     */
    public function getQueryData() {
        return $this->queryData;
    }
}
