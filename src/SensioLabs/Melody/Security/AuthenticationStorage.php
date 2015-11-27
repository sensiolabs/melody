<?php

namespace SensioLabs\Melody\Security;

/**
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 */
class AuthenticationStorage
{
    private $data = array();

    public function set($key, $data)
    {
        $this->data[$key] = $data;
    }

    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function all()
    {
        return $this->data;
    }

    public function replace(array $data)
    {
        $this->data = $data;
    }
}
