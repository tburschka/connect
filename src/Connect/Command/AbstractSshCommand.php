<?php

namespace Connect\Command;

use Connect\Bridge;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractSshCommand extends Command
{
    /**
     * @var Bridge
     */
    protected $bridge;

    protected function configure()
    {
        $this
            ->addOption(
                'hostname',
                null,
                InputOption::VALUE_REQUIRED,
                'Hostname to connect'
            )
            ->addOption(
                'port',
                null,
                InputOption::VALUE_OPTIONAL,
                'Port for connection',
                22
            )
            ->addOption(
                'timeout',
                null,
                InputOption::VALUE_OPTIONAL,
                'Timeout in seconds for connection (optional)',
                10
            )
            ->addOption(
                'username',
                null,
                InputOption::VALUE_OPTIONAL,
                'Username to connect with (optional)',
                $this->getUser()
            )
            ->addOption(
                'password',
                null,
                InputOption::VALUE_OPTIONAL,
                'Password or Passphrase (optional)'
            )
            ->addOption(
                'keyfile',
                null,
                InputOption::VALUE_OPTIONAL,
                'Keyfile for connection (optional)'
            )

        ;
    }

    /**
     * Returns the current user (could be wrong)
     * @return string
     */
    protected function getUser()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' || extension_loaded('posix')) {
            $processUser = posix_getpwuid(posix_geteuid());
            return $processUser['name'];
        } else {
            return get_current_user();
        }
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->bridge = new Bridge($output);

        $this->bridge->setHostname($input->getOption('hostname'));
        $this->bridge->setPort($input->getOption('port'));
        $this->bridge->setTimeout($input->getOption('timeout'));
        $this->bridge->setUsername($input->getOption('username'));

        if ($input->hasOption('password')) {
            $this->bridge->setPassword($input->getOption('password'));
        }

        if ($input->hasOption('keyfile')) {
            $this->bridge->setAuth(Bridge::AUTH_KEYFILE);
            $this->bridge->setKeyfile($input->getOption('keyfile'));
        } else {
            $this->bridge->setAuth(Bridge::AUTH_PASSWORD);
        }
    }
}