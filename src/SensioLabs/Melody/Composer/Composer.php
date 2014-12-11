<?php

namespace SensioLabs\Melody\Composer;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Composer
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class Composer
{
    /**
     * If composer executable has been guessed
     */
    private $guessComposer;

    public function __construct($composerExecutable = null)
    {
        if (!$composerExecutable) {
            $composerExecutable = $this->guessComposerExecutable();
        }

        if (!$composerExecutable) {
            throw new \RuntimeException('Impossible to find composer executable.');
        }

        $this->composerExecutable = $composerExecutable;
    }

    public function buildProcess(array $packages, $dir, $preferSource = false)
    {
        $args = $this->buildArgs(array('require'));

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
        $args = $this->buildArgs(array(
            'config',
            '--global',
            'vendor-dir',
        ));

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

    private function buildArgs(array $args)
    {
        array_unshift($args, $this->composerExecutable);

        if (!$this->guessComposer) {
            array_unshift($args, 'php');
        }

        return $args;
    }

    private function guessComposerExecutable()
    {
        $this->guessComposer = true;

        $finder = new ExecutableFinder();
        $finder->addSuffix('.phar');

        return $finder->find('composer');
    }
}
