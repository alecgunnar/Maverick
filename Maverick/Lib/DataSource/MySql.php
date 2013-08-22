<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class DataSource_MySql implements DataSource {
    /**
     * The current connection to the database
     *
     * @var \mysqli | null $connection
     */
    private static $connection = null;

    /**
     * The current query being worked with
     *
     * @var \mysqli_result | null $currentQuery
     */
    private $currentQuery = null;

    /**
     * Sets up a connection to the database
     *
     * @param  string $dbName=null
     * @return null
     */
    public function __construct($name=null) {
        if(is_null(self::$connection)) {
            $config = Maverick()->getConfig('database')->get('mysql')->get($name ?: 'default', true);

            self::$connection = new \mysqli($config['host'], $config['username'], $config['password'], $config['name']);
        }
    }

    /** 
     * Posts to a resource
     *
     * @param  mixed $params
     * @return mixed
     */
    public function post($params=null) {
        
    }

    /** 
     * Gets a resource -- only good for basic MySql queries if you need
     * to get more advanced, use: \Maverick\Lib\DataSource_MySql::query
     *
     * @param  array $params
     * @return \mysqli_result
     */
    public function get($params=null) {
        $query = "SELECT " . $params['select'] . " FROM " . $params['from'];

        if(array_key_exists('where', $params)) {
            $query .= " WHERE " . $params['where'];
        }

        if(array_key_exists('order', $params)) {
            $query .= " ORDER BY " . $params['order'];
        }

        if(array_key_exists('limit', $params)) {
            $params .= " LIMIT " . $params['limit'];
        }

        return $this->fetch($this->query($query));
    }

    /** 
     * Updates a resource
     *
     * @param  mixed $params
     * @return mixed
     */
    public function put($params=null) {
        
    }

    /** 
     * Deletes a resource
     *
     * @param  mixed $params
     * @return mixed
     */
    public function delete($params=null) {
        
    }

    /**
     * Runs a query
     *
     * throws \Exception
     * @param  string $query
     * @return \mysqli_result | boolean | null
     */
    public function query($query) {
        $query = self::$connection->query($query);

        if(!$query) {
            throw new \Exception('There was an error running your query. MySql Said: ' . self::$connection->error);
        }

        $this->currentQuery = $query;

        return $query;
    }

    /**
     * Fetches the result from the query
     *
     * @param  \mysqli_result | null $result
     * @return null
     */
    public function fetch(\mysqli_result $result=null) {
        $fetch = $this->currentQuery;

        if($result instanceof \mysqli_result) {
            $fetch = $result;
        }

        return $fetch->fetch_assoc();
    }
}