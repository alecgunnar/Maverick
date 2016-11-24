<?php

namespace Maverick\Http\Router\Route;

use InvalidArgumentException;

class Route
{
    /**
     * @var string[]
     */
    protected $methods;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $service;

    /**
     * @throws InvalidArgumentException
     * @param string[] $methods
     * @param string $path
     * @param string $service
     */
    public function __construct(array $methods, string $path, string $service)
    {
        $this->methods = $methods;
        $this->path = $path;
        $this->service = $service;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getService(): string
    {
        return $this->service;
    }
}
