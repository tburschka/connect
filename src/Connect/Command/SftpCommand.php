<?php

namespace Connect\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SftpCommand extends AbstractSshCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('sftp');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        $output->writeln('SFTP IS CURRENTLY A DUMMY!');
    }
}
