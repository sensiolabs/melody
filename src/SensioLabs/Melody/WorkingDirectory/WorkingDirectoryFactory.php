<?php

namespace SensioLabs\Melody\WorkingDirectory;

use Symfony\Component\Filesystem\Filesystem;

/**
 * WorkingDirectoryFactory
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class WorkingDirectoryFactory
{
    private $storagePath;
    private $filesystem;

    public function __construct($storagePath)
    {
        $this->storagePath = $storagePath;
        $this->filesystem = new Filesystem();

        $this->filesystem->mkdir($this->storagePath);
    }

    public function createTmpDir(array $packages, array $repositories)
    {
        $hash = $this->generateHash($packages, $repositories);

        $path = sprintf('%s/%s', $this->storagePath, $hash);

        return new WorkingDirectory($path, $this->filesystem);
    }

    private function generateHash(array $packages, array $repositories)
    {
        ksort($packages);

        if (empty($repositories)) {
            $config = $packages;
        } else {
            $config = array($repositories, $packages);
        }

        // Some application use `basename(__DIR__)` and may generate class
        // name with this dirname. And a sha256 hash may start with a number.
        // This will lead to a fatal error because PHP forbid that.
        return 'a'.hash('sha256', serialize($config));
    }
}
