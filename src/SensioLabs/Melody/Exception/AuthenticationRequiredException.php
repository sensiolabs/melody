<?php

namespace SensioLabs\Melody\Exception;

use SensioLabs\Melody\Handler\ResourceHandlerInterface;

/**
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 */
class AuthenticationRequiredException extends \LogicException
{
    private $handler;

    public function __construct(ResourceHandlerInterface $handler, $message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->handler = $handler;
    }

    /**
     * @return ResourceHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }
}
