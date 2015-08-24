<?php
/**
 * Maverick
 *
 * @author Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Loader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class YamlConfigLoader extends FileLoader
{
    const FILE_EXT = 'yml';

    public function load($resource, $type=null)
    {
        if (substr($resource, -3) != self::FILE_EXT) {
            $resource .= '.' . self::FILE_EXT;
        }

        return Yaml::parse($this->locator->locate($resource));
    }

    public function supports($resource, $type=null)
    {
        return is_string($resource) && pathinfo($resource, PATHINFO_EXTENSION) == self::FILE_EXT;
    }
}