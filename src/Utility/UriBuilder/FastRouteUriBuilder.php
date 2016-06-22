<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Utility\UriBuilder;

use FastRoute\RouteParser;
use Psr\Http\Message\UriInterface;
use InvalidArgumentException;
use GuzzleHttp\Psr7\Uri;
use Maverick\Router\Collection\RouteCollectionInterface;

class FastRouteUriBuilder implements UriBuilderInterface
{
    /**
     * @var RouteParser
     */
    protected $parser;

    /**
     * @var RouteCollectionInterface
     */
    protected $collection;

    /**
     * @param RouteParser $parser
     * @param RouteCollectionInterface $collection
     */
    public function __construct(RouteParser $parser, RouteCollectionInterface $collection)
    {
        $this->parser = $parser;
        $this->collection = $collection;
    }

    /**
     * @inheritDocs
     */
    public function build(string $name, array $params = []): UriInterface
    {
        $built = '';
        $route = $this->collection->getRoute($name);

        if ($route === null) {
            throw new InvalidArgumentException(
                'Cannot build URI for route: "' . $name . '" because it does not exist.'
            );
        }

        $versions = $this->parser->parse($route->getPath());
        $index    = 0;

        foreach ($versions as $index => $parts) {
            if ($index) {
                $built .= $this->processOptionalParam($parts, $name, $params);
            } else {
                $built .= $this->processRequiredParams($parts, $name, $params);
            }
        }

        return new Uri($built);
    }

    protected function processRequiredParams(array $parts, string $name, array $params): string
    {
        $built = '';

        foreach ($parts as $part) {
            if (is_array($part)) {
                $built .= $this->processParam($part, $name, $params);
            } else {
                $built .= $part;
            }
        }

        return $built;
    }

    protected function processOptionalParam(array $parts, string $name, array $params): string
    {
        $optional = $parts[count($parts) - 1];
        $built    = $this->processParam($optional, $name, $params, false);

        if (($len = strlen($built)) && $built[$len - 1] != '/') {
            $built = '/' . $built;
        }

        return $built;
    }

    protected function processParam(array $part, string $name, array $params, bool $strict = true): string
    {
        list($paramName, $regex) = $part;

        $paramValue = '';

        if (isset($params[$paramName])) {
            $paramValue = $params[$paramName];

            if (!preg_match('#' . $regex . '#', $paramValue)) {
                throw new InvalidArgumentException(
                    'Param value: "' . $paramValue . '" does not match expected format: "' . $regex . '" for route: "' . $name . '".'
                );
            }
        } elseif ($strict) {   
            throw new InvalidArgumentException(
                'Missing param: "' . $paramName . '" for route: "' . $name . '".'
            );
        }

        return $paramValue;
    }
}
