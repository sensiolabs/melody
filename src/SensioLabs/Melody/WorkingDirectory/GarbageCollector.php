<?php

namespace SensioLabs\Melody\WorkingDirectory;

use SensioLabs\Melody\Runner\Runner;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * GarbageCollector
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class GarbageCollector
{
    // One day
    const TTL = 86400;

    private $storePath;
    private $filesystem;
    private $finder;

    public function __construct($storePath)
    {
        $this->storePath = $storePath;
        $this->filesystem = new Filesystem();
        $this->finder = new Finder();
    }

    /**
     * Remove old workingDirectories
     */
    public function run()
    {
        $maxATime = time() - self::TTL;

        $files = Finder::create()
            ->in($this->storePath)
            ->depth(1)
            ->name(Runner::BOOTSTRAP_FILENAME)
            ->filter(function ($d) use ($maxATime) {
                return $d->getATime() < $maxATime;
            })
        ;

        $directories = array();

        foreach ($files as $file) {
            $directories[] = dirname($file);
        }

        $this->filesystem->remove($directories);
    }
}
