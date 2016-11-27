<?php

namespace Maverick\Console\Command\Build\Step;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use RuntimeException;

class InvalidateCacheBuildStep extends BuildStep
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $this->getRoot() . '/' . $this->directory;

        $output->writeln('Invalidating cache directory:');
        $output->writeln(sprintf("\tDirectory:\t%s", $directory));

        if (is_dir($directory)) {
            $rmrf = sprintf('rm -rf "%s"', $directory);
            system($rmrf);
        }

        mkdir($directory);
    }
}
