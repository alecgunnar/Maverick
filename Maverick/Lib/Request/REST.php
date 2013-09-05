<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Request_REST {
    /**
     * The url
     *
     * @var string | null
     */
    private $url = null;

    /**
     * The method
     *
     * @var string | null
     */
    private $method = null;

    /**
     * The parameters
     *
     * @var array
     */
    private $parameters = array();

    /**
     * The cUrl handle
     *
     * @var cUrl Handle | null
     */
    private $curl = null;

    /**
     * The response from the request
     *
     * @var mixed
     */
    private $response = null;

    /**
     * Set-up the new request
     *
     * @param  string | null $url
     * @param  string | null $method
     * @param  array  | null $parameters
     */
    public function __construct($url=null, $method=null, array $parameters=null) {
        $this->url    = $url;
        $this->setMethod($method);

        if(is_array($parameters)) {
            $this->parameters = $parameters;
        }

        $this->curl = curl_init($this->url);
    }

    /**
     * Sets the url
     *
     * @param  string $url
     * @return self
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Add a parameter
     *
     * @param  string $param
     * @param  mixed  $value
     * @return self
     */
    public function addParameter($param, $value) {
        $this->parameters[$param] = $value;

        return $this;
    }

    /**
     * Add parameters
     *
     * @param  array $params
     * @return self
     */
    public function addParameters(array $params) {
        $this->parameters = array_merge($this->parameters, $params);

        return $this;
    }

    /**
     * Set the method
     *
     * @param  string $method
     * @return self
     */
    public function setMethod($method) {
        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * Set a cUrl option
     *
     * @param  string $opt
     * @param  mixed  $value
     * @return self
     */
    public function setOption($opt, $value) {
        curl_setopt($this->curl, $opt, $value);
    }

    /**
     * Set cUrl options
     *
     * @param  string $opts
     * @return self
     */
    public function setOptions($opts) {
        curl_setopt_array($this->curl, $opts);
    }

    /**
     * Parses the parameters
     *
     * @return string
     */
    private function parseParameters() {
        $params = '';

        if(count($this->parameters)) {
            foreach($this->parameters as $key => $value) {
                if($params) {
                    $params .= '&';
                }

                $params .= $key . '=' . $value;
            }
        }

        return $params;
    }

    /**
     * Execute the request
     *
     * @return boolean
     */
    public function send() {
        if(is_null($this->url)) {
            throw new \Exception('Cannot make request, no URL supplied.');
        }

        switch($this->method) {
            case 'GET':
                return $this->get();
            case 'POST':
                return $this->post();
            default:
                throw new \Exception('Cannot make request, invalid or no method supplied.');
        }
    }

    /**
     * Gets the response
     *
     * @return mixed
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Executes the request
     *
     * @return boolean
     */
    private function execute() {
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        $this->response = curl_exec($this->curl);

        curl_close($this->curl);

        if($this->response === false) {
            return false;
        }

        return true;
    }

    /**
     * Send a GET request
     *
     * @return boolean
     */
    private function get() {
        $params = $this->parseParameters();
        $url    = $this->url . '?' . $params;

        $this->setOption(CURLOPT_URL, $url);

        return $this->execute();
    }

    /**
     * Send a POST request
     *
     * @return boolean
     */
    private function post() {
        $this->setOptions(array(CURLOPT_POST       => true,
                                CURLOPT_POSTFIELDS => $this->parseParameters()));

        return $this->execute();
    }
}