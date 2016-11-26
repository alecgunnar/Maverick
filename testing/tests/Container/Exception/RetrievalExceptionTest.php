<?php

namespace Maverick\Container\Exception;

use PHPUnit_Framework_TestCase;

class RetrievalExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testMessageIsBuildWithNameAndReason()
    {
        $name = 'service_name';
        $reason = 'circular reference';
        $message = 'The service "' . $name . '" could not be retrieved from the container. Reason: ' . $reason;

        $instance = new RetrievalException($name, $reason);

        $this->assertEquals($message, $instance->getMessage());
    }

    public function testMessageIsBuildWithNameOnly()
    {
        $name = 'service_name';
        $message = 'The service "' . $name . '" could not be retrieved from the container. Reason: Unknown';

        $instance = new RetrievalException($name);

        $this->assertEquals($message, $instance->getMessage());
    }
}
