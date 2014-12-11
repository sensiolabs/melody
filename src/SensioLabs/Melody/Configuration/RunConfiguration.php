<?php

namespace SensioLabs\Melody\Configuration;

/**
 * RunConfiguration.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class RunConfiguration
{
    private $noCache;
    private $preferSource;
    private $trusted;

    public function __construct($noCache = false, $preferSource = false, $trusted = false)
    {
        $this->noCache = $noCache;
        $this->preferSource = $preferSource;
        $this->trusted = $trusted;
    }

    public function noCache()
    {
        return $this->noCache;
    }

    public function preferSource()
    {
        return $this->preferSource;
    }

    public function isTrusted()
    {
        return $this->trusted;
    }
}
