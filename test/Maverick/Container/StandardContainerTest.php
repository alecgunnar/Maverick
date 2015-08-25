<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

use Maverick\Container\StandardContainer;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\FileLocator;

/**
 * @covers Maverick\Container\StandardContainer
 */
class StandardContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Maverick\Container\StandardContainer::create
     */
    public function testCreateLoadsServicesFromServiceConfigFile()
    {
        $locator = $this->getMockBuilder('Symfony\\Component\\Config\\FileLocator')->getMock();
        $file    = 'services.yml';

        $locator->expects($this->once())
            ->method('locate')
            ->with($file)
            ->will($this->returnValue(TEST_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $file));

        $instance = StandardContainer::create($locator);
    }

    /**
     * @covers Maverick\Container\StandardContainer::extend
     */
    public function testExtendOverridesExistingServices()
    {
        $service  = 'maverick.stdclass';
        $instance = StandardContainer::create(new FileLocator(ROOT_PATH . DIRECTORY_SEPARATOR . 'config'));

        // Initially the service should be as it is configured via the framework configuration
        $this->assertInstanceOf('stdclass', $instance->get($service));

        // Reset the service (get rid the instance which was just created)
        $instance->set($service, null);

        // Now load in the test service config which defined the $service to be a DateTime object
        $locator = $this->getMockBuilder('Symfony\\Component\\Config\\FileLocator')->getMock();
        $file    = 'services.yml';

        $locator->expects($this->once())
            ->method('locate')
            ->with($file)
            ->will($this->returnValue(TEST_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $file));

        $instance->extend($locator);

        $this->assertInstanceOf('DateTime', $instance->get($service));
    }
}