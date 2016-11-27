<?php

namespace Maverick\Console\Command\Build\Step;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use org\bovigo\vfs\vfsStream;
use RuntimeException;

class CacheContainerBuildStepTest extends PHPUnit_Framework_TestCase
{
    public function testCacheDirIsRemovedThenRecreated()
    {
        $dir = 'cache';
        $fqpn = FIXTURE_PATH . '/' . $dir;
        $file = 'container.php';
        $fqfn = $fqpn . '/' . $file;
        $namespace = 'cache';
        $class = 'cached_container';
        $fqcn = '\\' . $namespace . '\\' . $class;

        if (file_exists($fqfn)) {
            unlink($fqfn);
        } else if (!is_dir($fqpn)) {
            mkdir($fqpn);
        }

        $instance = new CacheContainerBuildStep($dir . '/' . $file, $namespace, $class);

        $instance->configure($this->getMockCommand(), 'dev', FIXTURE_PATH)
            ->execute($this->getMockInput(), $this->getMockOutput());

        $this->assertFileExists($fqfn);

        touch($fqfn);
        require_once($fqfn);

        $this->assertTrue(class_exists($fqcn));
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
