<?php

namespace Maverick\Console\Command\Build\Step;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BuildStep
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @param Command $command
     */
    final public function configure(Command $command)
    {
        $this->command = $command;

        $this->setup();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    abstract public function execute(InputInterface $input, OutputInterface $output);

    /**
     * @see Symfony\Component\Console\Command::addArgument This method is a proxy
     *
     * @return BuildStep
     */
    protected function addArgument(string $name, int $mode = null, string $description = '', $default = null)
    {
        $this->command->addArgument($name, $mode, $description, $default);
        return $this;
    }

    /**
     * @see Symfony\Component\Console\Command::addOption This method is a proxy
     *
     * @return BuildStep
     */
    protected function addOption(string $name, string $shortcut = null, int $mode = null, string $description = '', $default = null)
    {
        $this->command->addOption($name, $shortcut, $mode, $description, $default);
        return $this;
    }

    /**
     * Override this method to call addOption
     * and addArgument as needed.
     */
    protected function setup()
    {
        // Defaults to doing nothing
    }
}
