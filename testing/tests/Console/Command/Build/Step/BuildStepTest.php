<?php

namespace Maverick\Console\Command\Build\Step;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildStepTest extends PHPUnit_Framework_TestCase
{
    public function testConfigureSetsArgumentsAndOptions()
    {
        $opt['Name'] = 'test argument name';
        $opt['Short'] = 'test argument shortcut';
        $opt['Mode'] = 10;
        $opt['Desc'] = 'test argument description';
        $opt['Default'] = 'test argument default';

        $arg['Name'] = 'test option name';
        $arg['Mode'] = 10;
        $arg['Desc'] = 'test option description';
        $arg['Default'] = 'test option default';

        $command = $this->getMockCommand();

        $command->expects($this->once())
            ->method('addOption')
            ->with($opt['Name'], $opt['Short'], $opt['Mode'], $opt['Desc'], $opt['Default'])
            ->will($this->returnSelf());

        $command->expects($this->once())
            ->method('addArgument')
            ->with($arg['Name'], $arg['Mode'], $arg['Desc'], $arg['Default'])
            ->will($this->returnSelf());

        $instance = new class($opt, $arg) extends BuildStep {
            protected $optArgs;
            protected $argArgs;

            public function __construct(array $opt, array $arg)
            {
                $this->optArgs = array_values($opt);
                $this->argArgs = array_values($arg);
            }

            protected function setup(): void
            {
                $this->addOption(...$this->optArgs);
                $this->addArgument(...$this->argArgs);
            }

            public function execute(InputInterface $input, OutputInterface $output): void
            {

            }
        };

        $instance->configure($command, 'env', '/');
    }

    protected function getMockCommand()
    {
        return $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
