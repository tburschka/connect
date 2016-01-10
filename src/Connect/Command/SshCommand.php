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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ssh =$this->bridge->ssh();
        $ssh->exec($input->getArgument('exec'), function ($data) use ($output) {
            $output->write($data);
        });
        return intval(trim($ssh->exec('echo $?')));
    }
}
