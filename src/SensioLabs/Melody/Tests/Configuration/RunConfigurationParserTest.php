<?php

namespace SensioLabs\Melody\Tests\Configuration;

use SensioLabs\Melody\Configuration\RunConfigurationParser;

class RunConfigurationParserTest extends \PHPUnit_Framework_TestCase
{
    private $parser;

    public function setUp()
    {
        $this->parser = new RunConfigurationParser();
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
            array(array('packages' => array('symfony/symfony' => array())), 'The package at key "symfony/symfony" doesn\'t match the expected array structure.'),
            array(array('packages' => array('symfony/object' => new \stdClass())), 'The package at key "symfony/object" should either be a string or array, "object" given.'),
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
            'php: 5.5.0',
            'ext-pdo: *',
            array('symfony/debug' => '1.2'),
        )));

        $this->assertInstanceOf('SensioLabs\Melody\Configuration\ScriptConfiguration', $config);
        $expected = array(
            'symfony/finder' => '*',
            'symfony/console' => '1.2',
            'symfony/filesystem' => '1.3',
            'php' => '5.5.0',
            'ext-pdo' => '*',
            'symfony/debug' => '1.2',
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
                array(
                    'type' => 'vcs',
                    'url' => 'http://svn.example.org/projectA/',
                    'trunk-path' => 'Trunk',
                    'branches-path' => 'Branches',
                    'tags-path' => 'Tags',
                    'svn-cache-credentials' => false,
                ),
                array(
                    'type' => 'pear',
                    'url' => 'http://pear.foobar.repo',
                    'vendor-alias' => 'foobar',
                ),
                array(
                    'type' => 'package',
                    'package' => array(
                        'name' => 'smarty/smarty',
                        'version' => '3.1.7',
                        'dist' => array(
                            'url' => 'http://www.smarty.net/files/Smarty-3.1.7.zip',
                            'type' => 'zip',
                        ),
                        'source' => array(
                            'url' => 'http://smarty-php.googlecode.com/svn/',
                            'type' => 'svn',
                            'reference' => 'tags/Smarty_3_1_7/distribution/',
                        ),
                        'autoload' => array(
                            'classmap' => array('libs/'),
                        ),
                    ),
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
            array(
                'type' => 'vcs',
                'url' => 'http://svn.example.org/projectA/',
                'trunk-path' => 'Trunk',
                'branches-path' => 'Branches',
                'tags-path' => 'Tags',
                'svn-cache-credentials' => false,
            ),
            array(
                'type' => 'pear',
                'url' => 'http://pear.foobar.repo',
                'vendor-alias' => 'foobar',
            ),
            array(
                'type' => 'package',
                'package' => array(
                    'name' => 'smarty/smarty',
                    'version' => '3.1.7',
                    'dist' => array(
                        'url' => 'http://www.smarty.net/files/Smarty-3.1.7.zip',
                        'type' => 'zip',
                    ),
                    'source' => array(
                        'url' => 'http://smarty-php.googlecode.com/svn/',
                        'type' => 'svn',
                        'reference' => 'tags/Smarty_3_1_7/distribution/',
                    ),
                    'autoload' => array(
                        'classmap' => array('libs/'),
                    ),
                ),
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
