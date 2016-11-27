<?php

namespace Maverick;

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\DependencyInjection\Container;
use Maverick\Handler\Error\ErrorHandlerInterface;
use RuntimeException;

class BootstrapTest extends PHPUnit_Framework_TestCase
{
    public function testBootstrapLoadsContainerFromConfiguration()
    {
        $name = 'test_param';
        $value = 'test value';
        $root = vfsStream::setup();

        $this->addConfigFile($root, $name, $value);

        $container = \Maverick\bootstrap($root->url());

        $this->assertTrue($container->hasParameter($name));
        $this->assertEquals($value, $container->getParameter($name));
    }

    public function testBootstrapLoadsEnvironmentConfigIfItExists()
    {
        $name = 'test_param';
        $value = 'test value';
        $root = vfsStream::setup();

        $this->addConfigFile($root);
        $this->addConfigFile($root, $name, $value, 'environment.yml');

        $container = \Maverick\bootstrap($root->url());

        $this->assertTrue($container->hasParameter($name));
        $this->assertEquals($value, $container->getParameter($name));
    }

    public function testBootstrapThrowsExceptionIfConfigFileDoesNotExist()
    {
        $root = vfsStream::setup();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find the configuration file at: ' . $root->url() . '/config/config.yml');

        \Maverick\bootstrap($root->url());
    }

    public function testBootstrapLoadsCachedContainerWhenItExistsAndNotInDebugMode()
    {
        require_once(__DIR__ . '/../fixtures/CachedContainer.php');

        $root = vfsStream::setup();

        $container = \Maverick\bootstrap($root->url());

        $this->assertInstanceOf(\Cached\CachedContainer::class, $container);
    }

    public function testBootstrapDoesNotLoadCachedContainerWhenItExistsAndDebugModeIsEnabled()
    {
        require_once(__DIR__ . '/../fixtures/CachedContainer.php');

        $root = vfsStream::setup();
        $this->addConfigFile($root);

        $container = \Maverick\bootstrap($root->url(), true);

        $this->assertNotInstanceOf(\Cached\CachedContainer::class, $container);
    }

    public function testErrorHandlerIsRegistered()
    {
        require_once(__DIR__ . '/../fixtures/CachedContainer.php');

        $root = vfsStream::setup();

        $container = \Maverick\bootstrap($root->url());

        $this->assertEquals($container->get('error_handler'), set_error_handler(function () { }));
    }

    protected function addConfigFile(vfsStreamDirectory $root, string $name = 'test', string $value = 'value', string $file = 'config.yml', string $directory = 'config')
    {
        $format = <<<YAML
parameters:
    %s: %s
YAML;

        vfsStream::newFile($directory . DIRECTORY_SEPARATOR . $file)
            ->at($root)
            ->withContent(sprintf($format, $name, $value));
    }
}
