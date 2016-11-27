<?php

namespace Cached;

use Symfony\Component\DependencyInjection\Container;

class CachedContainer extends Container
{
    public function has($id)
    {
        return true;
    }

    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return new class {
            public function __call($name, $args)
            {
                if ($name == 'enable') {
                    set_error_handler($this);
                }
            }

            public function __invoke()
            {

            }
        };
    }
}
