<?php

namespace SensioLabs\Melody\Configuration;

use SensioLabs\Melody\Exception\ParseException;

/**
 * RunConfigurationParser.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class RunConfigurationParser
{
    const PACKAGE_DELIMITER = ':';
    const PACKAGE_REGEX = '/^((|[a-zA-Z0-9]([_.-]?[a-zA-Z0-9]+)*\\/[a-zA-Z0-9]([_.-]?[a-zA-Z0-9]+)*)|php|ext-[a-zA-Z0-9_.-]+)$/';

    public function parseConfiguration($config)
    {
        if (!is_array($config)) {
            throw new ParseException('The configuration should be an array.');
        }

        $packages = $this->parsePackages($config);
        $phpOptions = $this->parsePhpOptions($config);
        $repositories = $this->parseRepositories($config);

        return new ScriptConfiguration($packages, $phpOptions, $repositories);
    }

    private function parsePackages($config)
    {
        if (!array_key_exists('packages', $config)) {
            throw new ParseException('The configuration should define a "packages" key.');
        }

        if (!is_array($config['packages'])) {
            throw new ParseException('The packages configuration should be an array.');
        }

        $packages = array();

        foreach ($config['packages'] as $i => $package) {
            if (!is_string($package) && !is_array($package)) {
                throw new ParseException(sprintf('The package at key "%s" should either be a string or array, "%s" given', $i, gettype($package)));
            }

            if (is_array($package)) {
                // reformat JSON like
                // - "codeguy/arachnid": "1.*"
                // into
                // - "codeguy/arachnid: 1.*"
                if (count($package) == 1) {
                    $package = key($package).':'.current($package);
                } else {
                    throw new ParseException(sprintf('The package at key "%s" doesn\'t match the expected array structure.', $i));
                }
            }

            $packages[] = $this->extractPackage($package);
        }

        // allow empty list of config packages
        if ($packages) {
            $packages = call_user_func_array('array_merge', $packages);
        }

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

    private function parsePhpOptions($config)
    {
        if (!array_key_exists('php-options', $config)) {
            return array();
        }

        if (!is_array($config['php-options'])) {
            throw new ParseException('The php-options configuration should be an array.');
        }

        $phpOptions = array();

        foreach ($config['php-options'] as $i => $phpOption) {
            if (!is_string($phpOption)) {
                throw new ParseException(sprintf('The php-option at key "%s" should be a string.', $i));
            }

            $phpOptions[] = $phpOption;
        }

        return $phpOptions;
    }

    private function parseRepositories($config)
    {
        if (!array_key_exists('repositories', $config)) {
            return array();
        }

        if (!is_array($config['repositories'])) {
            throw new ParseException('The repositories configuration should be an array.');
        }

        return $config['repositories'];
    }
}
