<?php

namespace SensioLabs\Melody\Configuration;

/**
 * ScriptConfiguration
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class ScriptConfiguration
{
    private $packages;
    private $phpOptions;
    private $repositories;

    public function __construct(array $packages, array $phpOptions, array $repositories)
    {
        $this->packages = $packages;
        $this->phpOptions = $phpOptions;
        $this->repositories = $repositories;
    }

    public function getPackages()
    {
        return $this->packages;
    }

    public function getPhpOptions()
    {
        return $this->phpOptions;
    }

    public function getRepositories()
    {
        return $this->repositories;
    }
}
