<?php

namespace Maverick\Console\Command\Build\Step;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use RuntimeException;

class CopyScriptBuildStep extends BuildStep
{
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $source = sprintf('%s/app/%s.php', $this->getRoot(), $this->getEnvironment());
        $public = sprintf('%s/public/', $this->getRoot());
        $destination = sprintf('%s/index.php', $public);

        $output->writeln('Copying environment script:');
        $output->writeln(sprintf("\tSource:\t\t%s", $source));
        $output->writeln(sprintf("\tDestination:\t%s", $destination));

        if(!file_exists($source)) {
            $msg = sprintf('Could not find script for %s at %s.', $this->getEnvironment(), $source);
            throw new RuntimeException($msg);
        }

        if(!is_dir($public)) {
            mkdir($public);
        }

        copy($source, $destination);
    }
}
