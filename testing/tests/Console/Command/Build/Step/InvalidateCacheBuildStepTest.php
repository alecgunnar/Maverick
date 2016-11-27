<?php

namespace Maverick\Console\Command\Build\Step;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use org\bovigo\vfs\vfsStream;
use RuntimeException;

class InvalidateCacheBuildStepTest extends PHPUnit_Framework_TestCase
{
    public function testCacheDirIsRemovedThenRecreated()
    {
        $dir = 'cache';
        $fqn = FIXTURE_PATH . '/' . $dir;
        $file = $dir . '/file.txt';

        if (!is_dir($fqn)) {
            mkdir($fqn);
        }

        $deep = $fqn . '/a/ab/abc';

        mkdir($deep, 0777, true);
        touch($deep . '/file.txt');

        $instance = new InvalidateCacheBuildStep($dir);

        $instance->configure($this->getMockCommand(), 'dev', FIXTURE_PATH)
            ->execute($this->getMockInput(), $this->getMockOutput());

        $this->assertTrue(is_dir($fqn));
        $this->assertFalse(file_exists($file));
    }

    public function testCacheDirIsCreatedIfItDidntAlreadyExist()
    {
        $dir = 'cache';
        $fqn = FIXTURE_PATH . '/' . $dir;
        $file = $dir . '/file.txt';

        if (is_dir($fqn)) {
            rmdir($fqn);
        }

        $instance = new InvalidateCacheBuildStep($dir);

        $instance->configure($this->getMockCommand(), 'dev', FIXTURE_PATH)
            ->execute($this->getMockInput(), $this->getMockOutput());

        $this->assertTrue(is_dir($fqn));
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
