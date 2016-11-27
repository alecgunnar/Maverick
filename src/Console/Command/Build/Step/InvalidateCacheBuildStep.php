<?php

namespace Maverick\Console\Command\Build\Step;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

        $this->rmdir($directory);

        mkdir($directory, 0777, true);
    }

    protected function rmdir(string $directory) {
        if (!is_dir($directory)) {
            return;
        }

        foreach (glob($directory . '/*') as $target) {
            if (is_dir($target)) {
                $this->rmdir($target);
                continue;
            }

            unlink($target);
        }

        rmdir($directory);
    }
}
