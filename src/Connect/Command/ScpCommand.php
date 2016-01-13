<?php

namespace Connect\Command;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ScpCommand extends AbstractSshCommand
{

    const NET_SCP_LOCAL_FILE = 1;
    const NET_SCP_STRING = 2;

    /**
     * Configure SCP options
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('scp')
            ->addArgument(
                'method',
                InputArgument::REQUIRED,
                '"get" or "put" a file'
            )
            ->addOption(
                'localfile',
                null,
                InputOption::VALUE_REQUIRED,
                'Full local filename and path'
            )
            ->addOption(
                'remotefile',
                null,
                InputOption::VALUE_REQUIRED,
                'Full local filename and path'
            )
            ->addOption(
                'mode',
                null,
                InputOption::VALUE_OPTIONAL,
                'mode to transfer the file (in most cases file should work, else choose string)',
                'file'
            )
            ->addOption(
                'no-progress',
                null,
                InputOption::VALUE_NONE,
                'Hide progress bar'
            )
        ;
    }

    /**
     * Execute scp command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $method = $input->getArgument('method');
        if ('get' === strtolower($method)) {
            $this->bridge->scp()->get(
                $input->getOption('remotefile'),
                $input->getOption('localfile')
            );
        } elseif ('put' === strtolower($method)) {
            // check mode, modify localfile for transport
            $mode = $input->getOption('mode');
            if ('string' === $mode) {
                $mode = self::NET_SCP_STRING;
                $localfile = file_get_contents($input->getOption('localfile'));
                $size = strlen($localfile);
            } else { // 'file'
                $mode = self::NET_SCP_LOCAL_FILE;
                $localfile = $input->getOption('localfile');
                $size = filesize($localfile);
            }

            // init progressbar
            if ($input->getOption('no-progress')) {
                $progess = new ProgressBar(new NullOutput());
            } else {
                $progess = new ProgressBar($output, $size);
            }

            // transfer file
            $result = $this->bridge->scp()->put(
                $input->getOption('remotefile'),
                $localfile,
                $mode,
                function ($sent) use ($progess) {
                    $progess->setProgress($sent);
                }
            );

            if (!$input->getOption('no-progress')) {
                $output->writeln('');
            }
            if ($result) {
                $output->writeln('Successful transferred "' . $input->getOption('localfile') . '" to "' . $input->getOption('remotefile') . '"');
                return 0;
            } else {
                $output->writeln('Failed to transfer "' . $input->getOption('localfile') . '" to "' . $input->getOption('remotefile') . '"');
                return 1;
            }
        }
    }
}
