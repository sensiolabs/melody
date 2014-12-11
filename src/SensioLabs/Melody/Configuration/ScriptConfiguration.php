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

    public function __construct(array $packages, array $phpOptions)
    {
        $this->packages = $packages;
        $this->phpOptions = $phpOptions;
    }

    public function getPackages()
    {
        return $this->packages;
    }

    public function getPhpOptions()
    {
        return $this->phpOptions;
    }
}
