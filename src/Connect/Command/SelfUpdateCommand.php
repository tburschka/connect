<?php

namespace Connect\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(array('selfupdate'))
            ->setDescription('Updates connect to the latest version.')
            ->addOption('rollback', 'r', InputOption::VALUE_NONE, 'Revert to the previous installation of composer')
            ->setHelp(<<<EOT
The <info>self-update</info> command checks https://github.com/tburschka/connect for newer
versions of connect and if found, installs the latest.
<info>php connect.phar self-update</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connectPath = $_SERVER['HOME'] . '/.connect';
        $backupPath = $connectPath . '/backup';
        $localFilename = $_SERVER['PWD'] . '/' . basename($_SERVER['PHP_SELF']);
        $phar = 'connect.phar';

        if ($input->getOption('rollback')) {
            return $this->rollback($output, $connectPath, $localFilename);
        } else {
            $baseUrl = 'https://raw.githubusercontent.com/tburschka/connect/master/';
            $latestFile = @file_get_contents($baseUrl . $phar . '.sha256');
            $latestHash = substr($latestFile, 0, 64);
            $hash = hash_file('sha256', $localFilename);
            if ($hash !== $latestHash) {
                if (!file_exists($backupPath)) {
                    mkdir($backupPath, 0777, true);
                }
                copy($localFilename, $backupPath . '/' . $phar);
                file_put_contents($localFilename, file_get_contents($baseUrl . $phar));
                @chmod($localFilename, 0777 & ~umask());
                $output->writeln('Successful update to latest version');
            } else {
                $output->writeln('You already have the latest version');
            }
        }
    }

    protected function rollback(OutputInterface $output, $rollbackDir, $localFilename)
    {
        $rollbackFilename = $rollbackDir . '/backup/connect.phar';
        if (!file_exists($rollbackFilename)) {
            throw new \UnexpectedValueException('Connect rollback failed: no installation to roll back to in "' . $rollbackDir . '"');
        }
        if (!is_readable($rollbackFilename)) {
            throw new \FilesystemException('Connect rollback failed: "' . $rollbackFilename . '" could not be read');
        }
        if (!is_writeable($localFilename) || !copy($rollbackFilename, $localFilename)) {
            throw new \FilesystemException('Connect rollback failed: "' . $localFilename . '" could not be written to');
        }
        $output->writeln('Successful reverted to previous version');
        return 0;
    }
}
