<?php

namespace Maverick\Console\Command\Build\Step;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use org\bovigo\vfs\vfsStream;
use RuntimeException;

class CopyScriptBuildStepTest extends PHPUnit_Framework_TestCase
{
    public function testConfigIsMovedFromSourceToDestIfItExists()
    {
        $content = 'the expected script content';
        $environment = 'test';
        $root = vfsStream::setup();
        $source = vfsStream::newFile('app/' . $environment . '.php')->at($root)->withContent($content);
        $destination = vfsStream::newFile('public/index.php')->at($root)->withContent('bad content');

        $instance = new CopyScriptBuildStep();

        $instance->configure($this->getMockCommand(), $environment, $root->url())
            ->execute($this->getMockInput(), $this->getMockOutput());

        $this->assertEquals($content, $destination->getContent());
    }

    public function testExceptionThrownIfConfigurationDoesNotExist()
    {
        $environment = 'test';
        $root = vfsStream::setup()->url();
        $source = $root . '/app/' . $environment . '.php';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find script for ' . $environment . ' at ' . $source);

        $instance = new CopyScriptBuildStep();

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
