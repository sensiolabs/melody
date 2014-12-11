<?php

namespace SensioLabs\Melody\Exception;

use SensioLabs\Melody\Resource\Resource;

/**
 * TrustException.
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class TrustException extends \LogicException
{
    private $resource;

    public function __construct(Resource $resource, $message = 'You are running an untrusted resource', $code = 0, \Exception $e = null)
    {
        parent::__construct($message, $code, $e);
        $this->resource = $resource;
    }

    public function getResource()
    {
        return $this->resource;
    }
}
