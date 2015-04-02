<?php

namespace Connect\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SshCommand extends AbstractSshCommand
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
        // TODO: remove suppress as soon as https://github.com/phpseclib/phpseclib/issues/478 is solved
        $output->write(@$this->bridge->ssh()->exec($input->getArgument('exec')));
    }
}
