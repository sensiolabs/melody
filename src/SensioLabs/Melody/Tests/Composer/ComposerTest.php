<?php

namespace SensioLabs\Melody\Tests\Composer;

use SensioLabs\Melody\Composer\Composer;
use org\bovigo\vfs\vfsStream;

class ComposerTest extends \PHPUnit_Framework_TestCase
{
    private $composer;

    protected function setUp()
    {
        $this->composer = new Composer();
    }

    protected function tearDown()
    {
        $this->composer = null;
    }

    public function testBuildProcessWithoutPreferSource()
    {
        $packages = array(
            'symfony/symfony' => '*',
        );

        $process = $this->composer->buildProcess($packages, __DIR__);

        $this->assertStringEndsWith(" 'require' 'symfony/symfony:*' '--prefer-dist'", $process->getCommandLine());
    }

    public function testBuildProcessWithPreferSource()
    {
        $packages = array(
            'symfony/symfony' => '*',
        );

        $process = $this->composer->buildProcess($packages, __DIR__, true);

        $this->assertStringEndsWith(" 'require' 'symfony/symfony:*' '--prefer-source'", $process->getCommandLine());
    }

    public function testConfigureRepositoriesWithoutRepositories()
    {
        $workingDir = vfsStream::setup('workingDir');
        $repositories = array();

        $this->composer->configureRepositories($repositories, vfsStream::url('workingDir'));

        $this->assertFalse($workingDir->hasChild('composer.json'));
    }

    public function testConfigureRepositoriesWithRepositories()
    {
        vfsStream::setup('workingDir');
        $repositories = array(
            array(
                'type' => 'vcs',
                'url' => 'https://example.com/Pimple',
            ),
            array(
                'type' => 'vcs',
                'url' => 'https://example.com/melody',
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
                )
            ),
        );

        $this->composer->configureRepositories($repositories, vfsStream::url('workingDir'));

        $composerJsonContent = file_get_contents(vfsStream::url('workingDir/composer.json'));
        $composerJsonContentDecode = json_decode($composerJsonContent, true);
        $this->assertArrayHasKey('repositories', $composerJsonContentDecode);
        $this->assertEquals($repositories, $composerJsonContentDecode['repositories']);
    }
}
