<?php

namespace SensioLabs\Melody\Tests\Composer;

use SensioLabs\Melody\Composer\Composer;
use org\bovigo\vfs\vfsStream;

class ComposerTest extends \PHPUnit_Framework_TestCase
{
    private $composer;
    private $workingDirPath;

    protected function setUp()
    {
        vfsStream::setup('workingDir');
        $this->workingDirPath = vfsStream::url('workingDir');
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

        $process = $this->composer->buildProcess($packages, array(), $this->workingDirPath);

        $this->assertStringEndsWith(" 'update' '--prefer-dist'", $process->getCommandLine());
    }

    public function testBuildProcessWithPreferSource()
    {
        $packages = array(
            'symfony/symfony' => '*',
        );

        $process = $this->composer->buildProcess($packages, array(), $this->workingDirPath, true);

        $this->assertStringEndsWith(" 'update' '--prefer-source'", $process->getCommandLine());
    }

    public function testBuildProcessWithPackages()
    {
        $packages = array(
            'symfony/symfony' => '*',
        );

        $this->composer->buildProcess($packages, array(), $this->workingDirPath);

        $this->assertComposerJsonFileEquals(array('require' => $packages));
    }

    public function testBuildProcessWithRepositories()
    {
        $packages = array(
            'pimple/pimple' => '*',
            'sensiolabs/melody' => '*',
            'example/projectA' => '*',
            'foobar/TopLevelPackage1' => '*',
            'smarty/smarty' => '3.1.*',
        );
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
                ),
            ),
        );

        $this->composer->buildProcess($packages, $repositories, $this->workingDirPath, true);
        $this->assertComposerJsonFileEquals(
            array(
                'require' => $packages,
                'repositories' => $repositories,
            )
        );
    }

    private function assertComposerJsonFileEquals($expected)
    {
        $composerJsonContent = file_get_contents($this->workingDirPath.'/composer.json');
        $composerJsonContentDecode = json_decode($composerJsonContent, true);
        $this->assertEquals($expected, $composerJsonContentDecode);
    }
}
