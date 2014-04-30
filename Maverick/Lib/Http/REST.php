<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Http_REST {
    /**
     * The url
     *
     * @var string | null
     */
    private $url = null;

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
     */
    public function __construct($url) {
        $this->url  = $url;
        $this->curl = curl_init($this->url);
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
     * @param  array $opts
     * @return self
     */
    public function setOptions(array $opts) {
        curl_setopt_array($this->curl, $opts);
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
    private function makeRequest() {
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        $this->response = curl_exec($this->curl);

        curl_close($this->curl);

        if(!$this->response) {
            return false;
        }

        return true;
    }

    /**
     * Send a GET request
     *
     * @return boolean
     */
    public function get() {
        $this->setOption(CURLOPT_URL, $this->url . '?' . http_build_query($this->parameters));

        return $this->makeRequest();
    }

    /**
     * Send a POST request
     *
     * @return boolean
     */
    public function post() {
        $this->setOptions(array(CURLOPT_URL        => $this->url,
                                CURLOPT_POST       => true,
                                CURLOPT_POSTFIELDS => http_build_query($this->parameters)));

        return $this->makeRequest();
    }

    /**
     * Send a PUT request
     *
     * @return boolean
     */
    public function put() {
        $this->setOptions(array(CURLOPT_URL           => $this->url,
                                CURLOPT_CUSTOMREQUEST => 'PUT'
                                CURLOPT_POSTFIELDS    => http_build_query($this->parameters)));

        return $this->makeRequest();
    }

    /**
     * Send a DELETE request
     *
     * @return boolean
     */
    public function delete() {
        $this->setOptions(array(CURLOPT_URL           => $this->url . '?' . http_build_query($this->parameters),
                                CURLOPT_CUSTOMREQUEST => 'DELETE'));

        return $this->makeRequest();
    }
}