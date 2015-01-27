<?php

namespace Connect\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Ssh extends AbstractSsh
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('ssh')
            ->addArgument(
                'exec',
                InputArgument::REQUIRED,
                'execute command'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write($this->bridge->ssh()->exec($input->getArgument('exec')));
    }
}