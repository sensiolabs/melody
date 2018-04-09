<?php

namespace SensioLabs\Melody;

use SensioLabs\Melody\Composer\Composer;
use SensioLabs\Melody\Configuration\RunConfiguration;
use SensioLabs\Melody\Configuration\UserConfiguration;
use SensioLabs\Melody\Exception\TrustException;
use SensioLabs\Melody\Handler\FileHandler;
use SensioLabs\Melody\Handler\GistHandler;
use SensioLabs\Melody\Handler\StreamHandler;
use SensioLabs\Melody\Resource\LocalResource;
use SensioLabs\Melody\Resource\Resource;
use SensioLabs\Melody\Runner\Runner;
use SensioLabs\Melody\Script\ScriptBuilder;
use SensioLabs\Melody\Security\TokenStorage;
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
    private $scriptBuilder;
    private $composer;
    private $runner;

    public function __construct(TokenStorage $tokenStorage)
    {
        $storagePath = sprintf('%s/melody', sys_get_temp_dir());
        $this->garbageCollector = new GarbageCollector($storagePath);
        $this->handlers = array(
            new FileHandler(),
            new GistHandler($tokenStorage),
            new StreamHandler(),
        );
        $this->scriptBuilder = new ScriptBuilder();
        $this->wdFactory = new WorkingDirectoryFactory($storagePath);
        $this->composer = new Composer();
        $this->runner = new Runner($this->composer->getVendorDir());
    }

    public function run($resourceName, array $arguments, RunConfiguration $runConfiguration, UserConfiguration $userConfiguration, $cliExecutor)
    {
        $this->garbageCollector->run();

        $resource = $this->createResource($resourceName);
        $this->assertTrustedResource($resource, $runConfiguration, $userConfiguration);

        $script = $this->scriptBuilder->buildScript($resource, $arguments);

        $workingDirectory = $this->wdFactory->createTmpDir($script->getPackages(), $script->getRepositories());

        if ($runConfiguration->noCache()) {
            $workingDirectory->clear();
        }

        if ($workingDirectory->isNew()) {
            $workingDirectory->create();

            $process = $this->composer->buildProcess(
                $script->getPackages(),
                $script->getRepositories(),
                $workingDirectory->getPath(),
                $runConfiguration->preferSource()
            );

            $cliExecutor($process, true);

            // errors were already sent by the cliExecutor, just stop further processing
            if ($process->getExitCode() !== 0) {
                return;
            }

            $workingDirectory->lock();
        }

        $process = $this->runner->getProcess($script, $workingDirectory->getPath());

        return $cliExecutor($process, false);
    }

    /**
     * @param string $resourceName path to the resource
     *
     * @return Resource
     */
    private function createResource($resourceName)
    {
        foreach ($this->handlers as $handler) {
            if (!$handler->supports($resourceName)) {
                continue;
            }

            return $handler->createResource($resourceName);
        }

        throw new \LogicException(sprintf('No handler found for resource "%s".', $resourceName));
    }

    private function assertTrustedResource(Resource $resource, RunConfiguration $runConfiguration, UserConfiguration $userConfiguration)
    {
        if ($resource instanceof LocalResource
            || $runConfiguration->isTrusted()
            || in_array($resource->getMetadata()->getOwner(), $userConfiguration->getTrustedUsers())
            || in_array($resource->getSignature(), $userConfiguration->getTrustedSignatures())
        ) {
            return;
        }

        throw new TrustException($resource);
    }
}
