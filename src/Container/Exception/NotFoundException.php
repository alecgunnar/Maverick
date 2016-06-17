<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Container\Exception;

use Interop\Container\Exception\NotFoundException as ContainerNotFoundException;

class NotFoundException extends \Exception implements ContainerNotFoundException
{
    
}
