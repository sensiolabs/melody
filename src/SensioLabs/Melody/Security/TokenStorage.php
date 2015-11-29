<?php

namespace SensioLabs\Melody\Security;

/**
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 */
class TokenStorage
{
    private $tokens = array();

    public function set($key, Token $token)
    {
        $this->tokens[$key] = $token;
    }

    public function has($key)
    {
        return isset($this->tokens[$key]);
    }

    public function get($key)
    {
        return $this->has($key) ? $this->tokens[$key] : null;
    }

    public function all()
    {
        return $this->tokens;
    }
}
