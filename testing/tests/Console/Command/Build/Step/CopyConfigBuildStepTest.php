<?php

namespace Maverick\Console\Command\Build\Step;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use org\bovigo\vfs\vfsStream;
use RuntimeException;

class CopyConfigBuildStepTest extends PHPUnit_Framework_TestCase
{
    public function testConfigIsMovedFromSourceToDestIfItExists()
    {
        $content = 'the expected config content';
        $environment = 'test';
        $root = vfsStream::setup();
        $source = vfsStream::newFile('config/environment/' . $environment . '.yml')->at($root)->withContent($content);
        $destination = vfsStream::newFile('config/environment.yml')->at($root)->withContent('bad content');

        $instance = new CopyConfigBuildStep();

        $instance->configure($this->getMockCommand(), $environment, $root->url())
            ->execute($this->getMockInput(), $this->getMockOutput());

        $this->assertEquals($content, $destination->getContent());
    }

    public function testExceptionThrownIfConfigurationDoesNotExist()
    {
        $environment = 'test';
        $root = vfsStream::setup()->url();
        $source = $root . '/config/environment/' . $environment . '.yml';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find configuration for ' . $environment . ' at ' . $source);

        $instance = new CopyConfigBuildStep();

        $instance->configure($this->getMockCommand(), $environment, $root)
            ->execute($this->getMockInput(), $this->getMockOutput());
    }

    protected function getMockCommand()
    {
        return $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockInput()
    {
        return $this->getMockBuilder(InputInterface::class)
            ->getMock();
    }

    protected function getMockOutput()
    {
        return $this->getMockBuilder(OutputInterface::class)
            ->getMock();
    }
}
