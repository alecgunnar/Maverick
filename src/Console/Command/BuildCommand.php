<?php

namespace Maverick\Console\Command;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class BuildCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected function configure()
    {
        $this->setName('build')
            ->setDescription('Prepare the application for runtime')
            ->setHelp('Copies environment based configuration and scripts to their runtime locations.');

        $this->addArgument('environment', InputArgument::OPTIONAL, 'Which environment is being built for?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = $input->getArgument('environment') ?? 'prod';
        $buildingFor = sprintf('Building for %s', $environment);
        $output->writeln($buildingFor);

        $root = getcwd();
        $buildingRoot = sprintf('Building in %s', $root);
        $output->writeln($buildingRoot);

        $configFrom = sprintf('%s/config/environment/%s.yml', $root, $environment);
        $configTo = sprintf('%s/config/environment.yml', $root);
        $output->writeln('Copying environment config');
        copy($configFrom, $configTo);

        $indexFrom = sprintf('%s/app/%s.php', $root, $environment);
        $indexTo = sprintf('%s/public/index.php', $root);
        $output->writeln('Copying environment index');
        copy($indexFrom, $indexTo);

        $this->container = \Maverick\bootstrap($root);

        $this->assertContainerHasParameter('is_debug');
        $this->assertContainerHasParameter('cache_dir');

        if (!$this->container->get('is_debug')) {
            $output->writeln('Caching container');
            $output->writeln('Caching routes');
        } else {
            $output->writeln('Removing cache');
            rmdir($this->container->get('cache_dir'));
        }
    }

    protected function assertContainerHasParameter(string $name)
    {
        if (!$this->container->has($name)) {
            $message = sprintf('The parameter `%s` is not defined within the container, it must be defined!', $name);
            throw new Exception($message);
        }
    }
}
