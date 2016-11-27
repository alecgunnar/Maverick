<?php

namespace Maverick\Console\Command\Build;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Maverick\Console\Command\Build\Step\BuildStep;
use RuntimeException;

class BuildCommand extends Command
{
    /**
     * @var BuildStep[]
     */
    protected $buildSteps = [];

    /**
     * @param BuildStep $buildStep
     *
     * @return static
     */
    public function addBuildStep(BuildStep $buildStep)
    {
        $this->buildSteps[] = $buildStep;
        return $this;
    }

    protected function configure()
    {
        $this->setName('build')
            ->setDescription('Prepare the application for runtime')
            ->setHelp('Copies environment based configuration and scripts to their runtime locations.');

        $this->addOption('environment', 'env', InputOption::VALUE_REQUIRED, 'What environment would you like to build for?');
        $this->addOption('root', null, InputOption::VALUE_OPTIONAL, 'Where is the application located?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = $input->getOption('environment') ?? $_ENV['MAVERICK_ENVIRONMENT'] ?? false;
        $root = $input->getOption('root') ?? getcwd();

        if (!$environment) {
            throw new RuntimeException('Cannot determine build environment.');
        }

        foreach ($this->buildSteps as $buildStep) {
            if ($buildStep->shouldExecute($input)) {
                $buildStep->configure($this, $environment, $root)
                    ->execute($input, $output);
            }
        }
    }
}
