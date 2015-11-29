<?php

namespace SensioLabs\Melody\Configuration;

use SensioLabs\Melody\Security\SafeToken;
use SensioLabs\Melody\Security\Token;
use SensioLabs\Melody\Security\TokenStorage;

/**
 * UserConfiguration.
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class UserConfiguration
{
    private $trustedSignatures = array();
    private $trustedUsers = array();
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage = null)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function toArray()
    {
        $data = array(
            'trust' => array(
                'signatures' => $this->getTrustedSignatures(),
                'users' => $this->getTrustedUsers(),
            ),
        );

        if ($this->tokenStorage) {
            $safeTokens = array_filter($this->tokenStorage->all(), function (Token $token) {
                return $token instanceof SafeToken;
            });

            $data['security'] = array(
                'tokens' => array_map(function (SafeToken $token) {
                    return $token->getAttributes();
                }, $safeTokens),
            );
        }

        return $data;
    }

    public function load(array $data)
    {
        if (array_key_exists('trust', $data) && is_array($data['trust'])) {
            if (array_key_exists('signatures', $data['trust'])) {
                $this->trustedSignatures = (array) $data['trust']['signatures'];
            }
            if (array_key_exists('users', $data['trust'])) {
                $this->trustedUsers = (array) $data['trust']['users'];
            }
        }
        if (null !== $this->tokenStorage && isset($data['security']['tokens'])) {
            foreach ($data['security']['tokens'] as $key => $token) {
                $this->tokenStorage->set($key, new SafeToken($token));
            }
        }
    }

    public function getTrustedSignatures()
    {
        return $this->trustedSignatures;
    }

    public function addTrustedSignature($signature)
    {
        return $this->addTrustedSignatures(array($signature));
    }

    public function addTrustedSignatures(array $signatures)
    {
        $this->trustedSignatures = array_unique(
            array_merge(
                $this->trustedSignatures,
                $signatures
            )
        );

        return $this;
    }

    public function getTrustedUsers()
    {
        return $this->trustedUsers;
    }

    public function addTrustedUser($user)
    {
        return $this->addTrustedUsers(array($user));
    }

    public function addTrustedUsers(array $users)
    {
        $this->trustedUsers = array_unique(
            array_merge(
                $this->trustedUsers,
                $users
            )
        );

        return $this;
    }
}
