<?php

namespace SensioLabs\Melody\Tests\Configuration;

use SensioLabs\Melody\Configuration\ConfigurationParser;

class ConfigurationParserTest extends \PHPUnit_Framework_TestCase
{
    private $parser;

    public function setUp()
    {
        $this->parser = new ConfigurationParser();
    }

    public function tearDown()
    {
        $this->parser = null;
    }

    /**
     * @dataProvider providePackagesError
     */
    public function testParsePackagesError($packages, $exception)
    {
        $this->setExpectedException('SensioLabs\Melody\Exception\ParseException', $exception);

        $this->parser->parseConfiguration($packages);
    }

    public function providePackagesError()
    {
        return array(
            array('foobar', 'The configuration should be an array.'),
            array(array('foobar' => 'bar'), 'The configuration should define a "packages" key.'),
            array(array('packages' => 'string'), 'The packages configuration should be an array.'),
            array(array('packages' => array('symfony/symfony' => array())), 'The package at key "symfony/symfony" should be a string.'),
            array(array('packages' => array('symfony/symfony: 1 :1')), 'The package named "symfony/symfony" is not valid. It should contain only one ":".'),
            array(array('packages' => array('symfony/symfony/nope: 1')), 'The package named "symfony/symfony/nope" is not valid.'),
            array(array('packages' => array('symfony/symfony:')), 'The package version named "symfony/symfony" is not valid.'),
        );
    }

    public function testParsePackages()
    {
        $config = $this->parser->parseConfiguration(array('packages' => array(
            'symfony/finder',
            'symfony/console: 1.2',
            'symfony/filesystem',
            'symfony/filesystem: 1.3',
        )));

        $this->assertInstanceOf('SensioLabs\Melody\Configuration\ScriptConfiguration', $config);
        $expected = array(
            'symfony/finder' => '*',
            'symfony/console' => '1.2',
            'symfony/filesystem' => '1.3',
        );
        $this->assertSame($expected, $config->getPackages());
    }

    /**
     * @dataProvider providePhpOptionsError
     */
    public function testParsePhpOptionsError($phpOptions, $exception)
    {
        $this->setExpectedException('SensioLabs\Melody\Exception\ParseException', $exception);

        $this->parser->parseConfiguration($phpOptions);
    }

    public function providePhpOptionsError()
    {
        return array(
            array(array('packages' => array('symfony/symfony: 1'), 'php-options' => 'string'), 'The php-options configuration should be an array.'),
        );
    }

    public function testParsePhpOptions()
    {
        $config = $this->parser->parseConfiguration(array(
            'packages' => array(
                'symfony/finder',
            ),
            'php-options' => array(
                '-S',
                'localhost:8000',
            ),
        ));

        $this->assertInstanceOf('SensioLabs\Melody\Configuration\ScriptConfiguration', $config);
        $expected = array('-S', 'localhost:8000');
        $this->assertSame($expected, $config->getPhpOptions());
    }

    public function testParseRepositories()
    {
        $config = $this->parser->parseConfiguration(array(
            'packages' => array(
                'symfony/finder',
                'symfony/console',
            ),
            'repositories' => array(
                array(
                    'type' => 'vcs',
                    'url' => 'https://github.com/symfony/Finder.git',
                ),
                array(
                    'type' => 'vcs',
                    'url' => 'file:///home/symfony/Console',
                ),
            ),
        ));

        $this->assertInstanceOf('SensioLabs\Melody\Configuration\ScriptConfiguration', $config);
        $expected = array(
            array(
                'type' => 'vcs',
                'url' => 'https://github.com/symfony/Finder.git',
            ),
            array(
                'type' => 'vcs',
                'url' => 'file:///home/symfony/Console',
            ),
        );
        $this->assertSame($expected, $config->getRepositories());
    }

    public function testParseRepositoriesError()
    {
        $this->setExpectedException(
            'SensioLabs\Melody\Exception\ParseException',
            'The repositories configuration should be an array'
        );
        $config = array(
            'packages' => array(
                'symfony/finder',
            ),
            'repositories' => 'invalid',
        );

        $this->parser->parseConfiguration($config);
    }
}
