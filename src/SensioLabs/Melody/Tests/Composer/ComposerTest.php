<?php

namespace SensioLabs\Melody\Tests\Composer;

use SensioLabs\Melody\Composer\Composer;

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

    public function testBuildProcessWithComposerExecutable()
    {
        $this->composer = new Composer('path/to/composer.phar');

        $packages = array(
            'symfony/symfony' => '*',
        );

        $process = $this->composer->buildProcess($packages, __DIR__, true);

        $this->assertStringStartsWith("'php' 'path/to/composer.phar' 'require' 'symfony/symfony:*' ", str_replace('"', '\'', $process->getCommandLine()));
    }
}
