<?php

namespace Connect\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdate extends Command
{
    protected function configure()
    {
        $this
            ->setName(
                'self-update'
            )
            ->setAliases(array(
                'selfupdate'
            ))
            ->addOption(
                'revert',
                null,
                InputOption::VALUE_NONE,
                'Revert last saved version'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $backupPath = $_SERVER['HOME'] . '/.connect/backup/';
        $file = $_SERVER['PWD'] . '/' . basename($_SERVER['PHP_SELF']);

        if ($input->hasOption('revert') && $input->getOption('revert')) {
            copy($backupPath . 'connect.phar', $file);
            $output->writeln('Revert success');
        } else {
            $baseUrl = 'https://github.com/tburschka/connect/raw/master/';
            $latestFile = @file_get_contents($baseUrl . 'connect.phar.sha256');
            $latestHash = substr($latestFile, 0, 64);
            $hash = hash_file('sha256', $file);
            if ($hash !== $latestHash) {
                if (!file_exists($backupPath)) {
                    mkdir($backupPath, 0777, true);
                }
                copy($file, $backupPath . 'connect.phar');
                file_put_contents($file, file_get_contents($baseUrl . 'connect.phar'));
                $output->writeln('Update success');
            } else {
                $output->writeln('Already latest version');
            }
        }
    }
}