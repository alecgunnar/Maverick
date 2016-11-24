<?php

namespace Maverick\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    protected function configure()
    {
        $this->setName('build')
            ->setDescription('Prepare the application for runtime')
            ->setHelp('Copies environment based configuration and scripts to their runtime locations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
    }
}
