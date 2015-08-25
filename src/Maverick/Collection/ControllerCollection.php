<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Collection;

use Maverick\Controller\ControllerInterface;

class ControllerCollection
{
    private $controllers = [];

    public function add($name, ControllerInterface $controller)
    {
        $this->controllers[$name] = $controller;
    }

    public function get($name)
    {
        return isset($this->controllers[(string) $name]) ? $this->controllers[$name] : false;
    }
}