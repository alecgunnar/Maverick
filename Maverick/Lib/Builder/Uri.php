<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Uri {
    /**
     * Pulls apart a URI into each individual part
     *
     * @param  string $url
     * @return \Maverick\Lib\Model_Uri
     */
    public static function deconstructUri($uri) {
        $https     = false;
        $hostName  = '';
        $path      = '';
        $queryData = array();

        if(strpos($uri, 'https://') === 0) {
            $https = true;
        }

        $explodeUrl     = explode('?', $uri);
        $explodeLocator = explode('/', preg_replace('~http(s?)://~i', '', $explodeUrl[0]));

        $hostName = $explodeLocator[0];
        
        array_shift($explodeLocator);
        
        $path = str_replace(trim($_SERVER['SCRIPT_NAME'], '/'), '', implode('/', $explodeLocator));

        if(array_key_exists(1, $explodeUrl)) {
            $explodePairs = explode('&', $explodeUrl[1]);
            
            foreach($explodePairs as $pair) {
                $explodePair = explode('=', $pair);

                $queryData[$explodePair[0]] = '';

                if(array_key_exists(1, $explodePair)) {
                    $queryData[$explodePair[0]] = $explodePair[1];
                }
            }
        }

        return new Model_Uri($uri, $https, $hostName, $path, $queryData);
    }

    /**
     * Constructs the URI from the model
     *
     * @param  \Maverick\Lib\Model_Uri $model
     * @return string
     */
    public static function constructUri(\Maverick\Lib\Model_Uri $model) {
        $uri = 'http' . ($model->getHttps() ? 's' : '') . '://' . $model->getHostName() . '/' . $model->getResourcePath();

        if(count($model->getQueryData())) {
            $uri .= '?';
            $ct   = 0;

            foreach($model->getQueryData() as $k => $v) {
                if($ct) {
                    $uri .= '&';
                }

                $uri .= $k . '=' . $v;

                $ct++;
            }
        }

        return $uri;
    }
}