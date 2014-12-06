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

        if (is_writable(dirname($localFilename))) {
            $moveCallback = function ($tempFilename, $localFilename) {
                rename($tempFilename, $localFilename);
            };
        } elseif (is_writable($localFilename)) {
            $moveCallback = function ($tempFilename, $localFilename) {
                file_put_contents($localFilename, file_get_contents($tempFilename));
                unlink($tempFilename);
            };
        } else {
            $output->writeln(sprintf('<error>The file %s could not be written.</error>', $localFilename));
            $output->writeln('<error>Please run the self-update command with higher privileges.</error>');
            return 1;
        }

        $tempDirectory = sys_get_temp_dir();
        if (!is_writable($tempDirectory)) {
            $output->writeln(sprintf('<error>The temporary directory %s could not be written.</error>', $tempDirectory));
            $output->writeln('<error>Please run the self-update command with higher privileges.</error>');
            return 1;
        }

        $tempFilename = sprintf('%s/melody-%s.phar', $tempDirectory, uniqid());

        @copy($remoteFilename, $tempFilename);
        if (!file_exists($tempFilename)) {
            $output->writeln('<error>Unable to download new versions from the server.</error>');
            return 1;
        }

        chmod($tempFilename, 0777 & ~umask());
        try {
            // test the phar validity
            $phar = new \Phar($tempFilename);

            // free the variable to unlock the file
            unset($phar);
        } catch (\Exception $e) {
            unlink($tempFilename);
            $output->writeln(sprintf('<error>The download is corrupt (%s).</error>', $e->getMessage()));
            $output->writeln('<error>Please re-run the self-update command to try again.</error>');

            return 1;
        }

        call_user_func($moveCallback, $tempFilename, $localFilename);

        $output->writeln('<info>melody updated.</info>');
    }
}
