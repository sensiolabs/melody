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
    public function __construct($composerPath = null)
    {
        if (!$composerPath) {
            $composerPath = $this->guessComposerPath();
        }

        if (!$composerPath) {
            throw new \RuntimeException('Impossible to find composer executable.');
        }

        $this->composerPath = $composerPath;
    }

    public function buildProcess(array $packages, $dir, $preferSource = false)
    {
        $args = array(
            $this->composerPath,
            'require',
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
            ->getProcess()
        ;

        return $process;
    }

    public function getVendorDir()
    {
        $args = array(
            $this->composerPath,
            'config',
            '--global',
            'vendor-dir',
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

    private function guessComposerPath()
    {
        $finder = new ExecutableFinder();
        $finder->addSuffix('.phar');

        return $finder->find('composer');
    }
}
