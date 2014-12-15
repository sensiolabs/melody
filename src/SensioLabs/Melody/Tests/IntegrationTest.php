<?php

namespace SensioLabs\Melody\Tests;

use SensioLabs\Melody\Configuration\RunConfiguration;
use SensioLabs\Melody\Melody;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    private $fs;

    protected function setUp()
    {
        $this->fs = new Filesystem();
        $this->cleanCache();
    }

    protected function tearDown()
    {
        $this->cleanCache();
        $this->fs = null;
    }

    public function testRunWithDefaultOption()
    {
        $output = $this->melodyRun('hello-world.php');
        $this->assertContains('Loading composer repositories with package information', $output);
        $this->assertContains('Updating dependencies (including require-dev)', $output);
        $this->assertContains('Installing twig/twig (v1.16.0)', $output);
        $this->assertContains('Hello world', $output);
    }

    public function testRunWithShebang()
    {
        $output = $this->melodyRun('shebang.php');
        $this->assertContains('Loading composer repositories with package information', $output);
        $this->assertContains('Updating dependencies (including require-dev)', $output);
        $this->assertContains('Installing twig/twig (v1.16.0)', $output);
        $this->assertContains('Hello world', $output);
    }

    public function testRunWithCache()
    {
        $this->melodyRun('hello-world.php');
        $output = $this->melodyRun('hello-world.php');
        $this->assertSame('Hello world', $output);
    }

    public function testRunWithNoCache()
    {
        $this->melodyRun('hello-world.php');
        $output = $this->melodyRun('hello-world.php', array('no_cache' => true));
        $this->assertContains('Loading composer repositories with package information', $output);
        $this->assertContains('Updating dependencies (including require-dev)', $output);
        $this->assertContains('Installing twig/twig (v1.16.0)', $output);
        $this->assertContains('Hello world', $output);
    }

    public function testRunWithPreferSource()
    {
        $output = $this->melodyRun('pimple.php', array('prefer_source' => true));
        $this->assertContains('Loading composer repositories with package information', $output);
        $this->assertContains('Updating dependencies (including require-dev)', $output);
        $this->assertContains('Installing pimple/pimple (v1.0.2)', $output);
        $this->assertContains('Cloning', $output);
        $this->assertContains('value', $output);
    }

    public function testRunWithPhpOptions()
    {
        $output = $this->melodyRun('php-options.php');
        $this->assertContains('memory_limit=42M', $output);
    }

    private function melodyRun($fixture, array $options = array())
    {
        $melody = new Melody();

        $filename = sprintf('%s/Integration/%s', __DIR__, $fixture);

        $options = array_replace(array(
            'prefer_source' => false,
            'no_cache' => false,
            'composer_executable' => false,
        ), $options);

        $configuration = new RunConfiguration($options['no_cache'], $options['prefer_source'], $options['composer_executable']);

        $output = null;
        $cliExecutor = function (Process $process, $useProcessHelper) use (&$output) {
            $process->setTty(false);
            $process->mustRun(function ($type, $text) use (&$output) {
                $output .= $text;
            });
        };

        $melody->run($filename, array(), $configuration, $cliExecutor);

        return $output;
    }

    private function cleanCache()
    {
        $this->fs->remove(sys_get_temp_dir().'/melody');
    }
}
