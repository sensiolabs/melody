<?php

namespace SensioLabs\Melody\Configuration;

use SensioLabs\Melody\Exception\ConfigException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * UserConfigurationRepository.
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class UserConfigurationRepository
{
    public function __construct($storagePath = null)
    {
        $this->storagePath = $storagePath ?: $this->getStoragePath();
    }

    /**
     * Load stored configuration. Returns an empty UserConfiguration if no previous configuration was stored.
     *
     * @return UserConfiguration
     */
    public function load()
    {
        $config = new UserConfiguration();
        if (!file_exists($this->storagePath)) {
            return $config;
        }

        try {
            $data = Yaml::parse(file_get_contents($this->storagePath));
        } catch (ParseException $e) {
            throw new ConfigException(sprintf('The config file "%s" is not a valid YAML.', $this->storagePath), 0, $e);
        }

        if (is_array($data)) {
            $config->load($data);
        }

        return $config;
    }

    /**
     * Save the given configuration.
     *
     * @param UserConfiguration $config
     *
     * @return $this
     */
    public function save(UserConfiguration $config)
    {
        file_put_contents($this->storagePath, Yaml::dump($config->toArray(), 3, 2));

        return $this;
    }

    /**
     * Retrieves path to the user's HOME directory.
     *
     * @return string
     */
    private function getStoragePath()
    {
        $storagePath = getenv('MELODY_HOME');
        if (!$storagePath) {
            if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
                if (!getenv('APPDATA')) {
                    throw new \RuntimeException('The APPDATA or MELODY_HOME environment variable must be set for melody to run correctly');
                }
                $storagePath = strtr(getenv('APPDATA'), '\\', '/').'/Sensiolabs';
            } else {
                if (!getenv('HOME')) {
                    throw new \RuntimeException('The HOME or MELODY_HOME environment variable must be set for melody to run correctly');
                }
                $storagePath = rtrim(getenv('HOME'), '/').'/.sensiolabs';
            }
        }
        if (!is_dir($storagePath) && !@mkdir($storagePath, 0755, true)) {
            throw new \RuntimeException(sprintf('The directory "%s" does not exist and could not be created.', $storagePath));
        }
        if (!is_writable($storagePath)) {
            throw new \RuntimeException(sprintf('The directory "%s" is not writable.', $storagePath));
        }

        return $storagePath.'/melody.yml';
    }
}
