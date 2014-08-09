<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Http;

use Maverick\DataStructure\ReadOnlyMap,
    Maverick\DataStructure\UserInputMap;

class Request {
    /**
     * The headers for the current request
     *
     * @var Maverick\DataStructure\ReadOnlyMap
     */
    private $headers;

    /**
     * A list of environment vars for the request
     *
     * @var Maverick\DataStructure\ReadOnlyMap
     */
    private $env;

    /**
     * The current request method
     *
     * @var string
     */
    private $method;

    /** 
     * Is this a https request?
     *
     * @var boolean
     */
    private $https = false;

    /**
     * The url for the request
     *
     * @var string
     */
    private $url;

    /**
     * The urn for the request
     *
     * @var string
     */
    private $urn;

    /**
     * The query data for the request
     *
     * @var Maverick\DataStructure\ReadOnlyMap
     */
    private $queryData;

    /**
     * The post data for the request
     *
     * @var Maverick\DataStructure\ReadOnlyMap
     */
    private $data;

    /**
     * Constructor
     *
     * Pulls apart the $data sent in by separating
     * the headers from the general env vars.
     *
     * @param array $data=null
     */
    public function __construct($data=null) {
        $data    = $data !== null ? $data : $_SERVER;
        $headers = [];
        $env     = [];

        foreach($data as $key => $value) {
            switch($key) {
                case 'HTTPS':
                    if($value == 'on') {
                        $this->https = true;
                    }
                    continue 2;
                case 'REQUEST_METHOD':
                    $this->method = strtolower($value);
                    continue 2;
                case 'SERVER_NAME':
                    $this->url = trim($value, '/');
                    continue 2;
                case 'REQUEST_URI':
                    if($this->urn) break;
                case 'PATH_INFO':
                    $this->urn = '/' . trim($value, '/');
                    continue 2;
            }

            if(substr($key, 0, 4) == 'HTTP') {
                $headers[strtolower(substr($key, 5, strlen($key)))] = $value;
            } else {
                $env[strtolower($key)] = $value;
            }
        }

        $this->headers   = new UserInputMap($headers);
        $this->env       = new ReadOnlyMap($env);
        $this->queryData = new UserInputMap(isset($_GET) ? $_GET : []);
        $this->data      = new UserInputMap(isset($_POST) ? $_POST : []);

        if($this->method == 'post') {
            if(isset($_POST['__METHOD__'])) {
                $this->method = strtolower($_POST['__METHOD__']);
            }
        }
    }

    /**
     * Gets the headers map
     *
     * @codeCoverageIgnore
     * @return Maverick\DataStructure\ReadOnlyMap
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Gets the env vars map
     *
     * @codeCoverageIgnore
     * @return Maverick\DataStructure\ReadOnlyMap
     */
    public function getEnv() {
        return $this->env;
    }

    /**
     * Gets the current request method
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Gets whether or not this is https
     *
     * @codeCoverageIgnore
     * @return boolean
     */
    public function isHttps() {
        return $this->https;
    }

    /**
     * Gets the url
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Gets the urn
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getUrn() {
        return $this->urn;
    }

    /**
     * Gets the uri
     *
     * @return string
     */
    public function getUri() {
        return 'http' . ($this->https ? 's' : '') . '://' . $this->url . $this->urn;
    }

    /**
     * Gets the query data
     *
     * @codeCoverageIgnore
     * @return array
     */
    public function getQueryData() {
        return $this->queryData;
    }

    /**
     * Returns the data
     *
     * @codeCoverageIgnore
     * @return array
     */
    public function getData() {
        return $this->data;
    }
}