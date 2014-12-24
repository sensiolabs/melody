<?php

namespace SensioLabs\Melody\Composer;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Composer
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

    public function buildProcess(array $packages, $dir, $preferSource = false)
    {
        $args = array_merge(
            $this->composerCommand,
            array('require')
        );

        foreach ($packages as $package => $version) {
            $args[] = sprintf('%s:%s', $package, $version);
        }

        if ($preferSource) {
            $args[] = '--prefer-source';
        } else {
            $args[] = '--prefer-dist';
        }

        $process = ProcessBuilder::create($args)
            ->setWorkingDirectory($dir)
            ->setTimeout(240)
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
                return array($composerPath);
            }
        }

        foreach ($candidateNames as $candidateName) {
            $composerPath = sprintf('%s/%s', getcwd(), $candidateName);
            if (file_exists($composerPath)) {
                $phpFinder = new PhpExecutableFinder();
                return array_merge(
                    array(
                        $phpFinder->find(false),
                        $composerPath
                    ),
                    $phpFinder->findArguments()
                );
            }
        }

        return null;
    }
}
