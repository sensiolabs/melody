<?php

namespace SensioLabs\Melody\Script;

use SensioLabs\Melody\Configuration\ScriptConfiguration;
use SensioLabs\Melody\Resource\Resource;

/**
 * Script
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class Script
{
    private $resource;
    private $arguments;
    private $configuration;

    public function __construct(Resource $resource, array $arguments, ScriptConfiguration $configuration)
    {
        $this->resource = $resource;
        $this->arguments = $arguments;
        $this->configuration = $configuration;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function getPackages()
    {
        return $this->configuration->getPackages();
    }
}
