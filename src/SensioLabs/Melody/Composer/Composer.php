<?php

namespace SensioLabs\Melody\Composer;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Composer.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class Composer
{
    public function __construct(array $composerCommand = null)
    {
        if (!$composerCommand) {
            $composerCommand = $this->guessComposerCommand();
        }

        if (!$composerCommand) {
            throw new \RuntimeException('Impossible to find composer executable.');
        }

        $this->composerCommand = $composerCommand;
    }

    public function buildProcess(array $packages, array $repositories, $dir, $preferSource = false)
    {
        $this->generateJsonFile($packages, $repositories, $dir);

        return $this->updateProcess($dir, $preferSource);
    }

    private function generateJsonFile(array $packages, array $repositories, $dir)
    {
        $config = array(
            'require' => $packages,
        );
        if (!empty($repositories)) {
            $config['repositories'] = $repositories;
        }
        file_put_contents($dir.'/composer.json', json_encode($config));
    }

    private function updateProcess($dir, $preferSource = false)
    {
        $args = array_merge(
            $this->composerCommand,
            array('update')
        );

        if ($preferSource) {
            $args[] = '--prefer-source';
        } else {
            $args[] = '--prefer-dist';
        }

        $process = ProcessBuilder::create($args)
            ->setWorkingDirectory($dir)
            ->setTimeout(240)
            // forward the PATH variable from the user running the webserver, to the subprocess
            // so it can find binaries like e.g. composer
            ->setEnv('PATH', $_SERVER['PATH'])
            ->getProcess()
        ;

        return $process;
    }

    public function getVendorDir()
    {
        $args = array_merge(
            $this->composerCommand,
            array(
                'config',
                '--global',
                'vendor-dir',
            )
        );

        $process = ProcessBuilder::create($args)
            ->getProcess()
            ->mustRun()
        ;

        $output = trim($process->getOutput());

        // Composer could add a message telling the version is outdated like:
        // "This development build of composer is over 30 days old"
        // We should take the last line.
        $outputByLines = explode(PHP_EOL, $output);

        return end($outputByLines);
    }

    /**
     * Guess and build the command to call composer.
     *
     * Search:
     *  - a executable composer (or composer.phar)
     *  - a file composer (or composer.phar) prefixed by php
     */
    private function guessComposerCommand()
    {
        $candidateNames = array('composer', 'composer.phar');

        $executableFinder = new ExecutableFinder();
        foreach ($candidateNames as $candidateName) {
            if ($composerPath = $executableFinder->find($candidateName, null, array(getcwd()))) {
                return array(realpath($composerPath));
            }
        }

        foreach ($candidateNames as $candidateName) {
            $composerPath = sprintf('%s/%s', getcwd(), $candidateName);
            if (file_exists($composerPath)) {
                $phpFinder = new PhpExecutableFinder();

                return array_merge(
                    array(
                        $phpFinder->find(false),
                        $composerPath,
                    ),
                    $phpFinder->findArguments()
                );
            }
        }

        return;
    }

    /*
     * Copied from the official Composer repository.
     *
     * code: https://github.com/composer/composer/blob/master/src/Composer/Factory.php
     * author: Jordi Boggiano <j.boggiano@seld.be>
     * copyright holder: (c) 2015 Jordi Boggiano
     */
    public static function getComposerHomeDir()
    {
        $home = getenv('COMPOSER_HOME');
        if (!$home) {
            if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
                if (!getenv('APPDATA')) {
                    throw new \RuntimeException('The APPDATA or COMPOSER_HOME environment variable must be set for composer to run correctly');
                }
                $home = strtr(getenv('APPDATA'), '\\', '/').'/Composer';
            } else {
                if (!getenv('HOME')) {
                    throw new \RuntimeException('The HOME or COMPOSER_HOME environment variable must be set for composer to run correctly');
                }
                $home = rtrim(getenv('HOME'), '/').'/.composer';
            }
        }

        return $home;
    }
}
