<?php

namespace SensioLabs\Melody\Configuration;

/**
 * RunConfiguration
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class RunConfiguration
{
    private $noCache;
    private $preferSource;
    private $composerExecutable;

    public function __construct($noCache = false, $preferSource = false, $composerExecutable = false)
    {
        $this->noCache = $noCache;
        $this->preferSource = $preferSource;
        $this->composerExecutable = $composerExecutable;
    }

    public function noCache()
    {
        return $this->noCache;
    }

    public function preferSource()
    {
        return $this->preferSource;
    }

    public function composerExecutable()
    {
        return $this->composerExecutable;
    }
}
