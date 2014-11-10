<?php

namespace SensioLabs\Melody\WorkingDirectory;

use Symfony\Component\Filesystem\Filesystem;

/**
 * WorkingDirectory
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class WorkingDirectory
{
    private $path;
    private $filesystem;

    public function __construct($path, Filesystem $filesystem)
    {
        $this->path = $path;
        $this->filesystem = $filesystem;
    }

    public function isNew()
    {
        return !file_exists($this->getLockFile());
    }

    public function create()
    {
        $this->filesystem->mkdir($this->path);
    }

    public function clear()
    {
        $this->filesystem->remove($this->path);
        $this->create();
    }

    public function lock()
    {
        $this->filesystem->touch($this->getLockFile());
    }

    public function getPath()
    {
        return $this->path;
    }

    private function getLockFile()
    {
        return sprintf('%s/%s', $this->path, '.lock');
    }
}
