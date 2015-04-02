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
    }
}
