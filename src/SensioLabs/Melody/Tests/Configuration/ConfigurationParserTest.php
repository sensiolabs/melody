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
     * @expectedException SensioLabs\Melody\Exception\ParseException
     * @expectedExceptionMessage The configuration should be an array.
     */
    public function testParseNotAnArray()
    {
        $this->parser->parseConfiguration('foobar');
    }

    /**
     * @expectedException SensioLabs\Melody\Exception\ParseException
     * @expectedExceptionMessage The configuration should define a "packages" key.
     */
    public function testParseNotContainsPackagesKey()
    {
        $this->parser->parseConfiguration(array('foobar' => 'bar'));
    }

    /**
     * @expectedException SensioLabs\Melody\Exception\ParseException
     * @expectedExceptionMessage Packages configuration should be an array.
     */
    public function testParsePackagesKeyNotAnArray()
    {
        $this->parser->parseConfiguration(array('packages' => 'string'));
    }

    /**
     * @expectedException SensioLabs\Melody\Exception\ParseException
     * @expectedExceptionMessage The package at key "symfony/symfony" should be a string.
     */
    public function testParsePackageIsAnArray()
    {
        $this->parser->parseConfiguration(array('packages' => array('symfony/symfony' => array())));
    }

    /**
     * @expectedException SensioLabs\Melody\Exception\ParseException
     * @expectedExceptionMessage The package named "symfony/symfony" is not valid. It should contain only one ":".
     */
    public function testParseDoubleColon()
    {
        $this->parser->parseConfiguration(array('packages' => array('symfony/symfony: 1 :1')));
    }

    /**
     * @expectedException SensioLabs\Melody\Exception\ParseException
     * @expectedExceptionMessage The package named "symfony/symfony/nope" is not valid.
     */
    public function testParsePackageName()
    {
        $this->parser->parseConfiguration(array('packages' => array('symfony/symfony/nope: 1')));
    }

    /**
     * @expectedException SensioLabs\Melody\Exception\ParseException
     * @expectedExceptionMessage The package version named "symfony/symfony" is not valid.
     */
    public function testParseInvalidVersion()
    {
        $this->parser->parseConfiguration(array('packages' => array('symfony/symfony:')));
    }

    public function testParse()
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
}
