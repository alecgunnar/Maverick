<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Router\Entity\Factory;

use Maverick\Router\Entity\RouteEntityInterface;
use Maverick\Router\Entity\RouteEntity;

class RouteEntityFactory
{
    /**
     * @param string[] $methods = []
     * @param string $path = ''
     * @param mixed $handler = null
     */
    public function build(array $methods = [], string $path = '', $handler = null): RouteEntityInterface
    {
        return new RouteEntity($methods, $path, $handler);
    }
}
