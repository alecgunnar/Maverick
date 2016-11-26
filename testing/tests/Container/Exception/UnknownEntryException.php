<?php

namespace Maverick\Container\Exception;

use PHPUnit_Framework_TestCase;

class UnknownEntryExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testMessageIsBuildWithNameAndReason()
    {
        $name = 'service_name';
        $message = 'The service "' . $name . '" was not found in the container.';

        $instance = new UnknownEntryException($name);

        $this->assertEquals($message, $instance->getMessage());
    }
}
