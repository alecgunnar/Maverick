<?php

namespace Maverick;

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\DependencyInjection\Container;
use Maverick\Handler\Error\ErrorHandlerInterface;
use RuntimeException;

class BootstrapTest extends PHPUnit_Framework_TestCase
{
    public function testBootstrapLoadsContainerFromConfiguration()
    {
        $name = 'test_param';
        $value = 'test value';

        $root = $this->getRootPath($name, $value);

        $container = \Maverick\bootstrap($root);

        $this->assertTrue($container->hasParameter($name));
        $this->assertEquals($value, $container->getParameter($name));
    }

    public function testBootstrapThrowsExceptionIfConfigFileDoesNotExist()
    {
        $root = $this->getRootPath('test', 'value', 'configuration', 'application.yml');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find the configuration file at: ' . $root . '/config/config.yml');

        \Maverick\bootstrap($root);
    }

    public function testBootstrapLoadsCachedContainerWhenItExistsAndNotInDebugMode()
    {
        require_once(__DIR__ . '/../fixtures/CachedContainer.php');

        $root = $this->getRootPath();

        $container = \Maverick\bootstrap($root);

        $this->assertInstanceOf(\Cached\CachedContainer::class, $container);
    }

    public function testBootstrapDoesNotLoadCachedContainerWhenItExistsAndDebugModeIsEnabled()
    {
        require_once(__DIR__ . '/../fixtures/CachedContainer.php');

        $root = $this->getRootPath();

        $container = \Maverick\bootstrap($root, true);

        $this->assertNotInstanceOf(\Cached\CachedContainer::class, $container);
    }

    public function testErrorHandlerIsRegistered()
    {
        require_once(__DIR__ . '/../fixtures/CachedContainer.php');

        $root = $this->getRootPath();

        $container = \Maverick\bootstrap($root);

        $this->assertEquals($container->get('error_handler'), set_error_handler(function () { }));
    }

    protected function getRootPath(string $name = 'test', string $value = 'value', string $directory = 'config', string $file = 'config.yml')
    {
        $root = vfsStream::setup();

        $format = <<<YAML
parameters:
    %s: %s
YAML;

        vfsStream::newFile($directory . DIRECTORY_SEPARATOR . $file)
            ->at($root)
            ->withContent(sprintf($format, $name, $value));

        return $root->url();
    }
}
