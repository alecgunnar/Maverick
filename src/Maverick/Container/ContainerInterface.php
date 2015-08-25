<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Container;

use Symfony\Component\Config\FileLocatorInterface;

interface ContainerInterface
{
    public static function create(FileLocatorInterface $locator);

    public function extend(FileLocatorInterface $locator);

    public function set($name, $instance);

    public function get($name);

    public function has($name);
}