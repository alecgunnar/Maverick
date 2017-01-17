<?php

namespace Maverick\Console\Command\Build;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Maverick\Console\Command\Build\Step\BuildStep;
use RuntimeException;

class BuildCommandTest extends PHPUnit_Framework_TestCase
{
    public function testCommandCallsStepsInQueueOrder()
    {
        $stepA = new class extends BuildStep {
            public function execute(InputInterface $input, OutputInterface $output): void
            {
                $output->write('a');
            }
        };

        $stepB = new class extends BuildStep {
            public function execute(InputInterface $input, OutputInterface $output): void
            {
                $output->write('b');
            }
        };

        $instance = new BuildCommand();

        $instance->addBuildStep($stepA)
            ->addBuildStep($stepB);

        $tester = new CommandTester($instance);

        $tester->execute([
            '--environment' => 'test'
        ]);

        $this->assertEquals('ab', $tester->getDisplay());
    }

    public function testCommandCallsStepsInQueueOrderAndSkippedIfTheyDecideToBe()
    {
        $stepA = new class extends BuildStep {
            public function execute(InputInterface $input, OutputInterface $output): void
            {
                $output->write('a');
            }
        };

        $stepB = new class extends BuildStep {
            public function shouldExecute(InputInterface $input): bool
            {
                return false;
            }

            public function execute(InputInterface $input, OutputInterface $output): void
            {
                $output->write('b');
            }
        };

        $stepC = new class extends BuildStep {
            public function execute(InputInterface $input, OutputInterface $output): void
            {
                $output->write('c');
            }
        };

        $instance = new BuildCommand();

        $instance->addBuildStep($stepA)
            ->addBuildStep($stepB)
            ->addBuildStep($stepC);

        $tester = new CommandTester($instance);

        $tester->execute([
            '--environment' => 'test'
        ]);

        $this->assertEquals('ac', $tester->getDisplay());
    }

    public function testEnvironmentTakenFromOptionAndSentToSteps()
    {
        $step = new class extends BuildStep {
            public function execute(InputInterface $input, OutputInterface $output): void
            {
                $output->write($this->getEnvironment());
            }
        };

        $environment = 'env_name';
        $instance = new BuildCommand();

        $instance->addBuildStep($step);

        $tester = new CommandTester($instance);

        $tester->execute([
            '--environment' => $environment
        ]);

        $this->assertEquals($environment, $tester->getDisplay());
    }

    public function testEnvironmentTakenFromShortcutOptionAndSentToSteps()
    {
        $step = new class extends BuildStep {
            public function execute(InputInterface $input, OutputInterface $output): void
            {
                $output->write($this->getEnvironment());
            }
        };

        $environment = 'env_name';
        $instance = new BuildCommand();

        $instance->addBuildStep($step);

        $tester = new CommandTester($instance);

        $tester->execute([
            '-env' => $environment
        ]);

        $this->assertEquals($environment, $tester->getDisplay());
    }

    /**
     * @backupGlobals enabled
     */
    public function testEnvironmentTakenFromEnvIfNoOptionProvidedAndSentToSteps()
    {
        $step = new class extends BuildStep {
            public function execute(InputInterface $input, OutputInterface $output): void
            {
                $output->write($this->getEnvironment());
            }
        };

        $environment = $_ENV['MAVERICK_ENVIRONMENT'] = 'env_name';
        $instance = new BuildCommand();

        $instance->addBuildStep($step);

        $tester = new CommandTester($instance);

        $tester->execute([]);

        $this->assertEquals($environment, $tester->getDisplay());
    }

    public function testExceptionThrownIfEnvironmentCannotBeDetermined()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot determine build environment.');

        $instance = new BuildCommand();

        $tester = new CommandTester($instance);

        $tester->execute([]);
    }

    public function testRootDirectoryFromOptionUsedIfProvided()
    {
        $step = new class extends BuildStep {
            public function execute(InputInterface $input, OutputInterface $output): void
            {
                $output->write($this->getRoot());
            }
        };

        $root = '/root/path/to/application';
        $instance = new BuildCommand();

        $instance->addBuildStep($step);

        $tester = new CommandTester($instance);

        $tester->execute([
            '-env' => 'test',
            '--root' => $root
        ]);

        $this->assertEquals($root, $tester->getDisplay());
    }

    public function testRootDirectoryFromGetcwdIsUsedIfOptionNotProvided()
    {
        $step = new class extends BuildStep {
            public function execute(InputInterface $input, OutputInterface $output): void
            {
                $output->write($this->getRoot());
            }
        };

        $root = '/root/path/to/application';
        $instance = new BuildCommand();

        $instance->addBuildStep($step);

        $tester = new CommandTester($instance);

        $tester->execute([
            '-env' => 'test'
        ]);

        $this->assertEquals(getcwd(), $tester->getDisplay());
    }

    protected function getMockBuildStep()
    {
        return $this->getMockForAbstractClass(BuildStep::class);
    }
}
