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

    public function createTmpDir(array $packages)
    {
        $hash = $this->generateHash($packages);

        $path = sprintf('%s/%s', $this->storagePath, $hash);

        return new WorkingDirectory($path, $this->filesystem);
    }

    private function generateHash(array $packages)
    {
        ksort($packages);

        return hash('sha256', serialize($packages));
    }
}
