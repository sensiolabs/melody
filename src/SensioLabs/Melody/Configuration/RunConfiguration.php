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

    public function __construct($noCache = false, $preferSource = false)
    {
        $this->noCache = $noCache;
        $this->preferSource = $preferSource;
    }

    public function noCache()
    {
        return $this->noCache;
    }

    public function preferSource()
    {
        return $this->preferSource;
    }
}
