<?php

namespace SensioLabs\Melody\Tests;

use SensioLabs\Melody\Configuration\RunConfiguration;
use SensioLabs\Melody\Configuration\UserConfiguration;
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
        $output = $this->melodyRunFixture('hello-world.php');
        $this->assertContains('Loading composer repositories with package information', $output);
        $this->assertContains('Updating dependencies (including require-dev)', $output);
        $this->assertContains('Installing twig/twig (v1.16.0)', $output);
        $this->assertContains('Hello world', $output);
    }

    public function testRunWithShebang()
    {
        $output = $this->melodyRunFixture('shebang.php');
        $this->assertContains('Loading composer repositories with package information', $output);
        $this->assertContains('Updating dependencies (including require-dev)', $output);
        $this->assertContains('Installing twig/twig (v1.16.0)', $output);
        $this->assertNotContains('#!/usr/bin/env -S melody run', $output);
        $this->assertContains('Hello world', $output);
    }

    public function testRunWithCache()
    {
        $this->melodyRunFixture('hello-world.php');
        $output = $this->melodyRunFixture('hello-world.php');
        $this->assertSame('Hello world', $output);
    }

    public function testRunWithConstraints()
    {
        $output = $this->melodyRunFixture('hello-world-with-constraints.php');
        $this->assertContains('Hello world', $output);
    }

    public function testRunWithNoCache()
    {
        $this->melodyRunFixture('hello-world.php');
        $output = $this->melodyRunFixture('hello-world.php', array('no_cache' => true));
        $this->assertContains('Loading composer repositories with package information', $output);
        $this->assertContains('Updating dependencies (including require-dev)', $output);
        $this->assertContains('Installing twig/twig (v1.16.0)', $output);
        $this->assertContains('Hello world', $output);
    }

    public function testRunWithPreferSource()
    {
        $output = $this->melodyRunFixture('pimple.php', array('prefer_source' => true));
        $this->assertContains('Loading composer repositories with package information', $output);
        $this->assertContains('Updating dependencies (including require-dev)', $output);
        $this->assertContains('Installing pimple/pimple (v1.0.2)', $output);
        $this->assertContains('Cloning', $output);
        $this->assertContains('value', $output);
    }

    public function provideStreams()
    {
        return array(
            array('data', 'text/plain;base64,PD9waHANCjw8PENPTkZJRw0KcGFja2FnZXM6DQogICAgLSAidHdpZy90d2lnOjEuMTYuMCINCkNPTkZJRzsNCg0KJHR3aWcgPSBuZXcgVHdpZ19FbnZpcm9ubWVudChuZXcgVHdpZ19Mb2FkZXJfQXJyYXkoYXJyYXkoDQogICAgJ2ZvbycgPT4gJ0hlbGxvIHt7IGluY2x1ZGUoImJhciIpIH19JywNCiAgICAnYmFyJyA9PiAnd29ybGQnDQopKSk7DQoNCmVjaG8gJHR3aWctPnJlbmRlcignZm9vJyk7DQo='),
            array('phar', $this->getFixtureFile('hello-world.phar/hello-world.php')),
            array('compress.zlib', $this->getFixtureFile('hello-world.php.gz')),
        );
    }

    /**
     * @dataProvider provideStreams
     */
    public function testRunStream($protocol, $fixture)
    {
        $output = $this->melodyRunStream($protocol, $fixture, array('trust' => true));
        $this->assertContains('Hello world', $output);
    }

    public function testRunWithPhpOptions()
    {
        $output = $this->melodyRunFixture('php-options.php');
        $this->assertContains('memory_limit=42M', $output);
    }

    /**
     * @dataProvider provideGists
     */
    public function testRunGist($gist)
    {
        $output = $this->melodyRun($gist, array('trust' => true));
        $this->assertContains('Hello greg', $output);
    }

    public function provideGists()
    {
        return array(
            array('23bb3980daf65154c3d4'),
            array('lyricc/23bb3980daf65154c3d4'),
            array('https://gist.github.com/lyrixx/23bb3980daf65154c3d4'),
        );
    }

    /**
     * @expectedException \SensioLabs\Melody\Exception\TrustException
     */
    public function testRunGistUntrusted()
    {
        $this->melodyRun('23bb3980daf65154c3d4', array('trust' => false));
    }

    public function testRunWithForkRepositories()
    {
        $output = $this->melodyRunFixture('fork-repositories.php', array('prefer_source' => true));

        $this->assertContains('Loading composer repositories with package information', $output);
        $this->assertContains('Updating dependencies (including require-dev)', $output);
        $this->assertContains('Installing pimple/pimple (v1.0.2)', $output);
        $this->assertContains('Cloning', $output);
        $this->assertContains('value', $output);
    }

    private function melodyRunFixture($fixture, array $options = array())
    {
        return $this->melodyRun(sprintf('%s/Fixtures/%s', __DIR__, $fixture), $options);
    }

    private function melodyRunStream($protocol, $fixture, array $options = array())
    {
        $melody = new Melody();

        $filename = sprintf('%s://%s', $protocol, $fixture);

        $options = array_replace(array(
            'trust' => false,
            'prefer_source' => false,
            'no_cache' => false,
        ), $options);

        $runConfiguration = new RunConfiguration($options['no_cache'], $options['prefer_source'], $options['trust']);
        $userConfiguration = new UserConfiguration();

        $output = null;
        $cliExecutor = function (Process $process, $useProcessHelper) use (&$output) {
            $process->setTty(false);
            $process->mustRun(function ($type, $text) use (&$output) {
                $output .= $text;
            });
        };

        $melody->run($filename, array(), $runConfiguration, $userConfiguration, $cliExecutor);

        return $output;
    }

    private function melodyRun($filename, array $options = array())
    {
        $melody = new Melody();

        $options = array_replace(array(
            'trust' => false,
            'prefer_source' => false,
            'no_cache' => false,
        ), $options);

        $runConfiguration = new RunConfiguration($options['no_cache'], $options['prefer_source'], $options['trust']);
        $userConfiguration = new UserConfiguration();

        $output = null;
        $cliExecutor = function (Process $process, $useProcessHelper) use (&$output) {
            $process->setTty(false);
            $process->mustRun(function ($type, $text) use (&$output) {
                $output .= $text;
            });
        };

        $melody->run($filename, array(), $runConfiguration, $userConfiguration, $cliExecutor);

        return $output;
    }

    private function cleanCache()
    {
        $this->fs->remove(sys_get_temp_dir().'/melody');
    }

    private function getFixtureFile($fixtureName)
    {
        return sprintf('%s/Fixtures/%s', __DIR__, $fixtureName);
    }
}
