<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Utility\UriBuilder;

use Psr\Http\Message\UriInterface;

interface UriBuilderInterface
{
    /**
     * @throws InvalidArgumentException
     * @param string $routeName
     * @param mixed[] $params = []
     * @return UriInterface
     */
    public function build(string $routeName, array $params = []): UriInterface;
}
