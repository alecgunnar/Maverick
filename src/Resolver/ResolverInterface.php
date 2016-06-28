<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Resolver;

interface ResolverInterface
{
    /**
     * @throws RuntimeException
     * @param mixed $given
     * @return callable
     */
    public function resolve($given): callable;
}
