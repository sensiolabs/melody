<?php

namespace SensioLabs\Melody\Handler;

use SensioLabs\Melody\Exception\InvalidCredentialsException;

/**
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 */
interface AuthenticableHandlerInterface
{
    const CREDENTIALS_NORMAL = 'normal';
    const CREDENTIALS_SECRET = 'secret';

    /**
     * Returns an array of the required credentials.
     *
     * @return array
     */
    public function getRequiredCredentials();

    /**
     * Authenticates using given credentials and returns authentication data for storage or further use.
     *
     * @param array $credentials
     *
     * @return string|array
     *
     * @throws InvalidCredentialsException
     */
    public function authenticate(array $credentials);

    /**
     * Get the key allowing to identify this handler.
     *
     * @return string
     */
    public function getKey();
}
