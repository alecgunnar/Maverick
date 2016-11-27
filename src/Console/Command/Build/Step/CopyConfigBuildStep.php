<?php

namespace Maverick\Console\Command\Build\Step;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use RuntimeException;

class CopyConfigBuildStep extends BuildStep
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $source = sprintf('%s/config/environment/%s.yml', $this->getRoot(), $this->getEnvironment());
        $destination = sprintf('%s/config/environment.yml', $this->getRoot());

        $output->writeln('Copying environment config:');
        $output->writeln(sprintf("\tSource:\t\t%s", $source));
        $output->writeln(sprintf("\tDestination:\t%s", $destination));

        if(!file_exists($source)) {
            $msg = sprintf('Could not find configuration for %s at %s.', $this->getEnvironment(), $source);
            throw new RuntimeException($msg);
        }

        copy($source, $destination);
    }
}
