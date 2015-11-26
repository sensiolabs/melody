<?php

namespace SensioLabs\Melody\Configuration;

/**
 * UserConfiguration.
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class UserConfiguration
{
    private $trustedSignatures = array();
    private $trustedUsers = array();
    private $authenticationData = array();

    public function toArray()
    {
        return array(
            'trust' => array(
                'signatures' => $this->getTrustedSignatures(),
                'users' => $this->getTrustedUsers(),
            ),
            'auth' => $this->authenticationData,
        );
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
        if (array_key_exists('auth', $data) && is_array($data['auth'])) {
            $this->authenticationData = $data['auth'];
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

    public function setAuthenticationData($handlerKey, $data)
    {
        $this->authenticationData[$handlerKey] = $data;
    }

    public function getAuthenticationData($handlerKey)
    {
        return isset($this->authenticationData[$handlerKey]) ? $this->authenticationData[$handlerKey] : null;
    }
}
