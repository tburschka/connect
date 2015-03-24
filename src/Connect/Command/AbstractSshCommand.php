<?php

namespace Connect\Command;

use Connect\Bridge;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

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
                InputOption::VALUE_NONE,
                'Password or passphrase (optional)'
            )
            ->addOption(
                'passwordfile',
                null,
                InputOption::VALUE_OPTIONAL,
                'Password or passphrase stored in a file (optional)'
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
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && extension_loaded('posix')) {
            $processUser = posix_getpwuid(posix_geteuid());
            return $processUser['name'];
        } else {
            return get_current_user();
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('password')) {
            $helper = $this->getHelper('question');
            $question = new Question('Enter your password/passphrase: ');
            $question->setHidden(true);
            $question->setHiddenFallback(true);
            $password = $helper->ask($input, $output, $question);
            $this->bridge->setPassword($password);
        }
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->bridge = new Bridge($output);

        $this->bridge->setHostname($input->getOption('hostname'));
        $this->bridge->setPort($input->getOption('port'));
        $this->bridge->setTimeout($input->getOption('timeout'));
        $this->bridge->setUsername($input->getOption('username'));

        if ($input->getOption('password')) {
            $this->bridge->setAuth(Bridge::AUTH_PASSWORD);
        }

        if ($input->getOption('passwordfile')) {
            $this->bridge->setAuth(Bridge::AUTH_PASSWORD);
            $this->bridge->setPasswordfile($input->getOption('passwordfile'));
        }

        if ($input->getOption('keyfile')) {
            $this->bridge->setAuth(Bridge::AUTH_KEYFILE);
            $this->bridge->setKeyfile($input->getOption('keyfile'));
        }
    }
}
