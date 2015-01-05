<?php

namespace SensioLabs\Melody;

use SensioLabs\Melody\Composer\Composer;
use SensioLabs\Melody\Configuration\RunConfiguration;
use SensioLabs\Melody\Handler\FileHandler;
use SensioLabs\Melody\Handler\GistHandler;
use SensioLabs\Melody\Handler\StreamHandler;
use SensioLabs\Melody\Runner\Runner;
use SensioLabs\Melody\Script\ScriptBuilder;
use SensioLabs\Melody\WorkingDirectory\GarbageCollector;
use SensioLabs\Melody\WorkingDirectory\WorkingDirectoryFactory;

/**
 * Melody.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class Melody
{
    const VERSION = '1.0';

    private $garbageCollector;
    private $handlers;
    private $wdFactory;
    private $storagePath;
    private $scriptBuilder;
    private $composer;
    private $runner;

    public function __construct()
    {
        $storagePath = $this->storagePath = sprintf('%s/melody', sys_get_temp_dir());
        $this->garbageCollector = new GarbageCollector($storagePath);
        $this->handlers = array(
            new FileHandler(),
            new GistHandler(),
            new StreamHandler(),
        );
        $this->scriptBuilder = new ScriptBuilder();
        $this->wdFactory = new WorkingDirectoryFactory($storagePath);
        $this->composer = new Composer();
        $this->runner = new Runner($this->composer->getVendorDir());
    }

    public function run($filename, array $arguments, RunConfiguration $configuration, $cliExecutor)
    {
        $this->garbageCollector->run();

        $resource = $this->createResource($filename);

        $script = $this->scriptBuilder->buildScript($resource, $arguments);

        $workingDirectory = $this->wdFactory->createTmpDir($script->getPackages());

        if ($configuration->noCache()) {
            $workingDirectory->clear();
        }

        if ($workingDirectory->isNew()) {
            $workingDirectory->create();

            $process = $this->composer->buildProcess($script->getPackages(), $workingDirectory->getPath(), $configuration->preferSource());

            $cliExecutor($process, true);

            $workingDirectory->lock();
        }

        $process = $this->runner->getProcess($script, $workingDirectory->getPath());

        $cliExecutor($process, false);
    }
    
    /**
     * @return string Returns the base-path used for temp and working files
     */
    public function getStoragePath()
    {
        return $this->storagePath;
    }

    private function createResource($filename)
    {
        foreach ($this->handlers as $handler) {
            if (!$handler->supports($filename)) {
                continue;
            }

            return $handler->createResource($filename);
        }

        throw new \LogicException(sprintf('No handler found for resource "%s".', $filename));
    }
}
