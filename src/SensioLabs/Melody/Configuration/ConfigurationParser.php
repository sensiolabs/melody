<?php

namespace SensioLabs\Melody\Configuration;

use SensioLabs\Melody\Exception\ParseException;

/**
 * ConfigurationParser
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class ConfigurationParser
{
    const PACKAGE_DELIMITER = ':';
    const PACKAGE_REGEX = '/^(|[a-zA-Z0-9]([_.-]?[a-zA-Z0-9]+)*\\/[a-zA-Z0-9]([_.-]?[a-zA-Z0-9]+)*)$/';

    public function parseConfiguration($config)
    {
        if (!is_array($config)) {
            throw new ParseException('The configuration should be an array.');
        }

        $packages = $this->parsePackages($config);

        return new ScriptConfiguration($packages);
    }

    private function parsePackages($config)
    {
        if (!array_key_exists('packages', $config)) {
            throw new ParseException('The configuration should define a "packages" key.');
        }

        if (!is_array($config['packages'])) {
            throw new ParseException('Packages configuration should be an array.');
        }

        $packages = array();

        foreach ($config['packages'] as $i => $package) {
            if (!is_string($package)) {
                throw new ParseException(sprintf('The package at key "%s" should be a string.', $i));
            }

            $packages[] = $this->extractPackage($package);
        }

        $packages = call_user_func_array('array_merge', $packages);

        return $packages;
    }

    private function extractPackage($package)
    {
        if (false === strpos($package, self::PACKAGE_DELIMITER)) {
            $packageName = $this->validatePackage($package);

            return array($packageName => '*');
        }

        $explode = explode(self::PACKAGE_DELIMITER, $package);

        if (2 !== count($explode)) {
            throw new ParseException(sprintf('The package named "%s" is not valid. It should contain only one ":".', $explode[0]));
        }

        $packageName = $this->validatePackage($explode[0]);

        $version = trim($explode[1]);

        if (!$version) {
            throw new ParseException(sprintf('The package version named "%s" is not valid.', $explode[0]));
        }

        return array($packageName => $version);
    }

    private function validatePackage($package)
    {
        if (!preg_match(self::PACKAGE_REGEX, $package)) {
            throw new ParseException(sprintf('The package named "%s" is not valid.', $package));
        }

        return trim($package);
    }

}
