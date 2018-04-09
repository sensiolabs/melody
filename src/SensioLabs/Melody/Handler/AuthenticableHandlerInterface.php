<?php

namespace SensioLabs\Melody\Handler;

use SensioLabs\Melody\Exception\InvalidCredentialsException;
use SensioLabs\Melody\Security\Token;

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
     * The array should be like:
     *
     * array(
     *     'username' => AuthenticableHandlerInterface::CREDENTIALS_NORMAL,
     *     'password' => AuthenticableHandlerInterface::CREDENTIALS_SECRET,
     * );
     *
     * or array('token') if none of the information are particularly sensitive (defaults to AuthenticableHandlerInterface::CREDENTIALS_NORMAL).
     *
     * @return array
     */
    public function getRequiredCredentials();

    /**
     * Authenticates using given credentials and returns a security token for further use.
     *
     * @param array $credentials
     *
     * @return Token
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
