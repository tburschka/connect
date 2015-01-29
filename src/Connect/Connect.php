<?php

namespace Connect;

use Connect\Command\ScpCommand;
use Connect\Command\SelfUpdateCommand;
use Connect\Command\SftpCommand;
use Connect\Command\SshCommand;
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
        $defaultCommands[] = new ScpCommand();
        $defaultCommands[] = new SftpCommand();
        $defaultCommands[] = new SshCommand();
        $defaultCommands[] = new SelfUpdateCommand();
        return $defaultCommands;
    }
}
