<?php

namespace SensioLabs\Melody\Security;

/**
 * Token holds attributes for authentication purpose during execution.
 *
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 */
class Token
{
    private $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
