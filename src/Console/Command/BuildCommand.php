<?php

namespace Maverick\Console\Command;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
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

        $cacheDir = sprintf('%s/%s', $root, $this->container->get('cache_dir'));
        $cacheDirExists = is_dir($cacheDir);

        if (!$this->container->get('is_debug')) {
            if (!$cacheDirExists) {
                $createCacheDir = sprintf('Creating cache directory %s', $cacheDir);
                $output->writeln($createCacheDir);

                mkdir($cacheDir);
            }

            $output->writeln('Caching container');
            $this->assertContainerHasParameter('container_cache_file');

            $containerCacheFile = sprintf('%s/%s', $cacheDir, $this->container->get('container_cache_file'));

            $cachableContainer = $this->container->get('service_container');
            $cachableContainer->compile();

            $dumper = new PhpDumper($cachableContainer);
            $cached = $dumper->dump([
                'class' => 'CachedContainer'
            ]);

            file_put_contents($containerCacheFile, $cached);

            $cachingContainer = sprintf('Wrote container cache to %s', $containerCacheFile);
            $output->writeln($cachingContainer);

            $this->assertContainerHasParameter('router_cache_file');

            $output->writeln('Caching routes');

            $this->container->get('fast_route.cached_dispatcher');

            $cachingRouter = sprintf('Wrote router cache to %s/%s', $cacheDir, $this->container->get('router_cache_file'));
            $output->writeln($cachingRouter);
        } elseif($cacheDirExists) {
            $removeCacheDir = sprintf('Removing cache directory %s', $cacheDir);
            $output->writeln($removeCacheDir);

            $cmd = sprintf('rm -rf %s', $cacheDir);
            system($cmd);
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
