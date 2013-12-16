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
            $config = \Maverick\Maverick::getConfig('database')->get('mysql')->get($name ?: 'default', true);

            self::$connection = new \mysqli($config['host'], $config['username'], $config['password'], $config['name']);
        }
    }

    /**
     * Gets the MySQLi connection
     *
     * @return \MySqli
     */
    public static function getConnection() {
        return self::$connection;
    }

    /** 
     * Posts to a resource
     *
     * @throws Exception
     * @param  array  $params=null
     * @param  array | null  $where=null
     * @param  string $table=null
     */
    public function post($params=null, $where=null, $table=null) {
        if(!is_array($params)) {
            throw new \Exception('Function post expects parameter 1 to be an array');
        }

        if(!is_string($table)) {
            throw new \Exception('Function post expects parameter 3 to be a string');
        }

        if(!is_null($where)) {
            if(!is_array($where)) {
                throw new \Exception('Function post expects parameter 2 to be an array');
            } else {
                $whereQuery = ' WHERE ';
                $i          = 0;

                foreach($where as $c => $v) {
                    if($i) {
                        $whereQuery .= ' && '; 
                    }

                    $whereQuery .= '`' . $c . '` = "' . $v . '"';

                    $i++;
                }
            }
        }

        $set   = '';
        $setTo = '';

        foreach($params as $c => $v) {
            if($set) {
                $set .= ', '; 
            }

            if(is_null($v)) {
                $setTo = 'null';
            } else {
                $setTo = '"' . $v . '"';
            }

            $set .= '`' . $c . '` = ' . $setTo;
        }

        $this->query('UPDATE `' . $table . '` SET ' . $set . $whereQuery);
    }

    /** 
     * Gets a resource -- only good for basic MySql queries if you need
     * to get more advanced, use: \Maverick\Lib\DataSource_MySql::query
     *
     * @throws \Exception
     * @param  array  $params=null
     * @param  string $useModel=null
     * @return array
     */
    public function get($params=null, $useModel=null) {
        if(!is_array($params)) {
            throw new \Exception('Function get expects parameter 1 to be an array');
        }

        if(!is_null($useModel) && !is_string($useModel)) {
            throw new \Exception('Function get expects parameter 2 to be a string');
        }

        if(!array_key_exists('select', $params)) {
            $params['select'] = '*';
        }

        $query = "SELECT " . $params['select'] . " FROM " . $params['from'];

        if(array_key_exists('where', $params)) {
            if(is_array($params['where'])) {
                $where = '';

                foreach($params['where'] as $c => $v) {
                    if($where) {
                        $where .= ' && '; 
                    }

                    $where .= '`' . $c . '` = "' . $v . '"';
                }

                $params['where'] = $where;
            }

            $query .= " WHERE " . $params['where'];
        }

        if(array_key_exists('order', $params)) {
            $query .= " ORDER BY " . $params['order'];
        }

        if(array_key_exists('limit', $params)) {
            $query .= " LIMIT " . $params['limit'];
        }

        $result = $this->query($query);
        $return = array();

        if(is_string($useModel)) {
            $model      = '\Application\Model\\' . $useModel;
            $doWithData = function($data) use($model) { return new $model($data); };
        } else {
            $doWithData = function($data) { return $data; };
        }

        while($row = $this->fetch($result)) {
            $return[] = $doWithData($row);
        }

        $result->free();

        return $return;
    }

    /** 
     * Updates a resource
     *
     * @throws \Exception
     * @param  mixed  $params=null
     * @param  string $table
     * @return boolean | integer
     */
    public function put($params=null, $table=null) {
        if(!is_array($params)) {
            throw new \Exception('Function put expects parameter 1 to be an array');
        }

        if(!is_string($table)) {
            throw new \Exception('Function put expects parameter 2 to be a string');
        }

        $columns = '';
        $values  = '';
        $giveVal = '';

        foreach($params as $col => $val) {
            if($columns && $values) {
                $columns .= ',';
                $values  .= ',';
            }

            if(is_null($val)) {
                $giveVal = 'null';
            } else {
                $giveVal = '"' . $val . '"';
            }

            $columns .= '`' . $col . '`';
            $values  .= $giveVal;
        }

        $this->query('INSERT INTO `' . $table . '` (' . $columns . ') VALUES (' . $values . ')');

        if(self::$connection->insert_id) {
            return self::$connection->insert_id;
        }

        return false;
    }

    /** 
     * Deletes a resource
     *
     * @throws \Exception
     * @param  array  $params=null
     * @param  string $table=null
     */
    public function delete($params=null, $table=null) {
        if(!is_array($params)) {
            throw new \Exception('Function delete expects parameter 1 to be an array');
        }

        if(!is_string($table)) {
            throw new \Exception('Function delete expects parameter 2 to be a string');
        }

        $where = '';

        foreach($params as $c => $v) {
            if($where) {
                $where .= ' && '; 
            }

            $where .= '`' . $c . '` = "' . $v . '"';
        }

        $this->query('DELETE FROM `' . $table . '` WHERE ' . $where);
    }

    /**
     * Runs a query
     *
     * throws  \Exception
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

    /**
     * Escapes data for the resource
     *
     * @param  string | array $str
     * @return string | array
     */
    public function escape($str) {
        if(is_array($str)) {
            $escaped = $str;

            if(count($str)) {
                foreach($str as $key => $value) {
                    $escaped[$key] = $this->escape($value);
                }
            }

            return $escaped;
        }

        return self::$connection->real_escape_string($str);
    }
}