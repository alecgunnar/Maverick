<?php

use Maverick\DependencyManagement\ServiceManager;

class ServiceManagerTest extends PHPUnit_Framework_TestCase {
    public function testConstruct() {
        $obj = new ServiceManager();

        $this->assertAttributeInstanceOf('Maverick\DataStructure\Map', 'services', $obj);
        $this->assertAttributeInstanceOf('Maverick\DataStructure\Map', 'objects', $obj);
    }

    public function testRegisterService() {
        $obj = new ServiceManager();

        $name     = 'test.service';
        $callback = function() {
            return new ServiceManager;
        };

        $obj->register($name, $callback);

        $this->assertEquals($callback, $obj->getServices()->get($name));
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testRegisterServiceWithInvalidName() {
        $obj = new ServiceManager();

        $obj->register(false, function() { });
    }

    /**
     * @expectedException Maverick\Exception\DuplicateValueException
     */
    public function testRegisterExistingService() {
        $obj = new ServiceManager();

        $name = 'test.duplicate.service';

        $obj->register($name, function() { });
        $obj->register($name, function() { });
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testRegisterServiceWithInvalidCallback() {
        $obj = new ServiceManager();

        $obj->register('service', false);
    }

    public function testGetService() {
        $obj = new ServiceManager();

        $name     = 'test.service';
        $instance = new ServiceManager;
        $callback = function() use($instance) {
            return $instance;
        };

        $obj->register($name, $callback);

        $this->assertEquals($instance, $obj->get($name));
        $this->assertEquals($obj->get($name), $obj->get($name));
    }

    /**
     * @expectedException Maverick\Exception\UnknownValueException
     */
    public function testGetUnknownServiceThrowsException() {
        $obj  = new ServiceManager();
        $obj->get('unknown.service');
    }

    public function testGetNewInstance() {
        $obj      = new ServiceManager();
        $name     = 'test.service';
        $callback = function() {
            return new ServiceManager;
        };

        $obj->register($name, $callback);

        $this->assertNotSame($obj->getNew($name), $obj->getNew($name));
    }

    /**
     * @expectedException Maverick\Exception\UnknownValueException
     */
    public function testGetNewUnknownServiceThrowsException() {
        $obj = new ServiceManager();
        $obj->getNew('unknown.service');
    }

    public function testCallServiceMethod() {
        $obj  = new ServiceManager;
        $name = 'test.service';

        $mock = $this->getMock('Maverick\Application');
        $mock->expects($this->once())
            ->method('finish');

        $obj->register($name, function() use($mock) {
            return $mock;
        });

        $obj->call($name . '->finish');
    }

    /**
     * @expectedException Maverick\Exception\InvalidTypeException
     */
    public function testValidateCallStringForNonStringThrowsException() {
        $obj = new ServiceManager;
        $obj->call(false);
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testValidateCallStringForImproperFormatThrowsException() {
        $obj = new ServiceManager;
        $obj->call('service>method');
    }

    /**
     * @expectedException Maverick\Exception\UnknownValueException
     */
    public function testValidateCallStringForUnknownServiceThrowsException() {
        $obj = new ServiceManager;
        $obj->call('service.name->method');
    }

    public function testCallServiceMethodReturnsMethodReturnValue() {
        $obj    = new ServiceManager;
        $name   = 'test.service';
        $return = 'test return value';

        $mock = $this->getMock('Maverick\Application');
        $mock->expects($this->once())
            ->method('finish')
            ->willReturn($return);

        $obj->register($name, function() use($mock) {
            return $mock;
        });

        $this->assertEquals($return, $obj->call($name . '->finish'));
    }

    public function testCallServiceMethodGetsArguments() {
        $obj  = new ServiceManager;
        $name = 'test.service';
        $arg  = 'test argument value';

        $mock = $this->getMock('Maverick\Application');
        $mock->expects($this->once())
            ->method('finish')
            ->will($this->returnArgument(0));

        $obj->register($name, function() use($mock) {
            return $mock;
        });

        $this->assertEquals($arg, $obj->call($name . '->finish', [$arg]));
    }

    public function testReplaceService() {
        $obj  = new ServiceManager;
        $name = 'test.service';

        $obj->register($name, function() {
            return new ServiceManager();
        });

        $replaceWithObj = new ServiceManager();

        $obj->replace($name, function() use($replaceWithObj) {
            return $replaceWithObj;
        });

        $this->assertSame($replaceWithObj, $obj->get($name));
    }

    /**
     * @expectedException Maverick\Exception\UnknownValueException
     */
    public function testReplaceUnknownServiceThrowsException() {
        $obj = new ServiceManager;

        $obj->register('test.service', function() {
            return new ServiceManager();
        });

        $obj->replace('test.unknown.service', function() { });
    }

    /**
     * @expectedException Maverick\Exception\InvalidValueException
     */
    public function testReplaceServiceWithInvalidTypeThrowsException() {
        $obj  = new ServiceManager;
        $name = 'test.service';

        $obj->register($name, function() {
            return new ServiceManager();
        });

        $obj->replace($name, function() {
            return new ServiceManagerTest();
        });
    }

    /**
     * @expectedException Maverick\Exception\InvalidOperationException
     */
    public function testReplacingInstantiatedServiceThrowsException() {
        $obj  = new ServiceManager;
        $name = 'test.service';

        $obj->register($name, function() {
            return new ServiceManager();
        });

        $obj->get($name);

        $obj->replace($name, function() {
            return ServiceManager();
        });
    }
}