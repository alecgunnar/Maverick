<?php

namespace Maverick\Console\Command\Build\Step;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

class CacheContainerBuildStep extends BuildStep
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param string $file
     * @param string $namespace
     * @param string $class
     */
    public function __construct(string $file, string $namespace, string $class)
    {
        $this->file = $file;
        $this->namespace = $namespace;
        $this->class = $class;
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $file = sprintf('%s/%s', $this->getRoot(), $this->file);
        
        $output->writeln('Caching the container:');
        $output->writeln(sprintf("\tFile:\t\t%s", $file));

        $container = \Maverick\bootstrap($this->getRoot(), true);
        $container->compile();

        $dumper = new PhpDumper($container);
        $dumped = $dumper->dump([
            'namespace' => $this->namespace,
            'class' => $this->class
        ]);

        file_put_contents($file, $dumped);
    }
}
