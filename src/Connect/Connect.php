<?php

namespace Connect;

use Connect\Command\Scp;
use Connect\Command\Sftp;
use Connect\Command\Ssh;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class Connect extends Application
{

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new Scp();
        $defaultCommands[] = new Sftp();
        $defaultCommands[] = new Ssh();
        return $defaultCommands;
    }
}