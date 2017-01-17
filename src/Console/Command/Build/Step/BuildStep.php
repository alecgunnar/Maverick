<?php

namespace Maverick\Console\Command\Build\Step;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BuildStep
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var string
     */
    private $root;

    /**
     * @param Command $command
     * @param string $environment
     *
     * @return static
     */
    final public function configure(Command $command, string $environment, string $root): BuildStep
    {
        $this->command = $command;
        $this->environment = $environment;
        $this->root = $root;

        $this->setup();

        return $this;
    }

    /**
     * @param InputInterface $input
     *
     * @return bool
     */
    public function shouldExecute(InputInterface $input): bool
    {
        return true;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    abstract public function execute(InputInterface $input, OutputInterface $output): void;

    /**
     * @see Symfony\Component\Console\Command::addArgument This method is a proxy
     *
     * @return BuildStep
     */
    protected function addArgument(string $name, int $mode = null, string $description = '', $default = null): BuildStep
    {
        $this->command->addArgument($name, $mode, $description, $default);
        return $this;
    }

    /**
     * @see Symfony\Component\Console\Command::addOption This method is a proxy
     *
     * @return BuildStep
     */
    protected function addOption(string $name, string $shortcut = null, int $mode = null, string $description = '', $default = null): BuildStep
    {
        $this->command->addOption($name, $shortcut, $mode, $description, $default);
        return $this;
    }

    /**
     * @return string
     */
    protected function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @return string
     */
    protected function getRoot(): string
    {
        return $this->root;
    }

    /**
     * Override this method to call addOption
     * and addArgument as needed.
     */
    protected function setup(): void
    {
        // Defaults to doing nothing
    }
}
