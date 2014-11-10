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

    public function __construct(array $packages)
    {
        $this->packages = $packages;
    }

    public function getPackages()
    {
        return $this->packages;
    }
}
