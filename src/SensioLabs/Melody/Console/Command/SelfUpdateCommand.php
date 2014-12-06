<?php

namespace SensioLabs\Melody\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Igor Wiedler <igor@wiedler.ch>
 * @author Stephane PY <py.stephane1@gmail.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class SelfUpdateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Update melody.phar to the latest version.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command replace your melody by the
latest version from melody.sensiolabs.org.

<info>php melody %command.name%</info>

EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $remoteFilename = 'http://get.sensiolabs.org/melody.phar';
        $localFilename = $_SERVER['argv'][0];
        $tempFilename = basename($localFilename, '.phar').'-tmp.phar';
        if (false === @file_get_contents($remoteFilename)) {
            $output->writeln('<error>Unable to download new versions from the server.</error>');

            return 1;
        }

        try {
            copy($remoteFilename, $tempFilename);
            chmod($tempFilename, 0777 & ~umask());

            // test the phar validity
            $phar = new \Phar($tempFilename);
            // free the variable to unlock the file
            unset($phar);
            rename($tempFilename, $localFilename);

            $output->writeln('<info>melody updated.</info>');
        } catch (\Exception $e) {
            if (!$e instanceof \UnexpectedValueException && !$e instanceof \PharException) {
                throw $e;
            }
            unlink($tempFilename);
            $output->writeln(sprintf('<error>The download is corrupt (%s).</error>', $e->getMessage()));
            $output->writeln('<error>Please re-run the self-update command to try again.</error>');

            return 1;
        }
    }
}
