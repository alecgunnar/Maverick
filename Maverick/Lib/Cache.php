<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Cache {
    /**
     * The current cache key being worked with
     *
     * @var string | null
     */
    private $cacheKey = null;

    /**
     * The data file
     *
     * @var string | null
     */
    private $dataFile = null;

    /**
     * The expire time file
     *
     * @var string | null
     */
    private $expiresFile = null;

    /**
     * Starts-up the cache class
     *
     * @param  string  $cacheKey
     * @param  integer $expiresAfter
     */
    public function __construct($cacheKey, $expiresAfter=-1) {
        $this->cacheKey = $cacheKey;

        if(!is_integer($expiresAfter)) {
            throw new \Exception('Parameter two for method ' . __NAMESPACE__ . '\Cache::__construct must be an integer.');
        }

        $cacheLoc = \Maverick\Maverick::getConfig('Cache')->get('path');
        $checkDir = function($dir) { if(!is_dir($dir)) mkdir($dir); };

        $checkDir($cacheLoc);
        $cacheLoc .= DS . $cacheKey;
        $checkDir($cacheLoc);
        $cacheLoc .= DS;

        $this->dataFile    = $cacheLoc . 'data.txt';
        $this->expiresFile = $cacheLoc . 'expires.txt';
        $expired           = false;

        if(!file_exists($this->dataFile)) {
            touch($this->dataFile);
        }

        if(!file_exists($this->expiresFile)) {
            touch($this->expiresFile);
        }

        $expires = (int)file_get_contents($this->expiresFile);

        if(is_integer($expires)) {
            $lastEdit = filemtime($this->dataFile);

            if((time() - $lastEdit) > $expiresAfter && $expiresAfter >= 0) {
                file_put_contents($this->dataFile, '');
            }
        }

        if(!$expiresAfter || $expires != $expiresAfter) {
            file_put_contents($this->expiresFile, $expiresAfter);
        }
    }

    /**
     * Set the cache
     *
     * @param  mixed $data
     * @return boolean
     */
    public function set($data) {
        $serialized    = serialize($data);
        $writeDataFile = file_put_contents($this->dataFile, $serialized);

        if($writeDataFile === false) {
            return false;
        }

        touch($this->dataFile);

        return true;
    }

    /**
     * Get the cache
     *
     * @param  mixed $data
     * @return mixed
     */
    public function get() {
        $data = file_get_contents($this->dataFile);

        if($data !== false) {
            return unserialize($data);
        }

        return false;
    }
}