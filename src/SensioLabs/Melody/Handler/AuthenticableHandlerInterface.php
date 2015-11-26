<?php

namespace SensioLabs\Melody\Handler;

use SensioLabs\Melody\Configuration\UserConfiguration;
use SensioLabs\Melody\Exception\InvalidCredentialsException;

/**
 * Interface AuthenticableHandlerInterface.
 *
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 */
interface AuthenticableHandlerInterface extends ResourceHandlerInterface
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
     * Provides credentials to be used at some point by the handler.
     *
     * @param array             $credentials
     * @param UserConfiguration $userConfig
     *
     * @throws InvalidCredentialsException
     */
    public function provideCredentials(array $credentials, UserConfiguration $userConfig);
}
