<?php

namespace SensioLabs\Melody\Tests\Configuration;

use SensioLabs\Melody\Configuration\UserConfiguration;
use SensioLabs\Melody\Configuration\UserConfigurationRepository;

class UserConfigurationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $repository = new UserConfigurationRepository(__DIR__.'/../Fixtures/config.yml');
        $config = $repository->load();

        $this->assertInstanceOf('SensioLabs\Melody\Configuration\UserConfiguration', $config);
        $this->assertEquals(array('foo', 'bar'), $config->getTrustedSignatures());
        $this->assertEquals(array('baz', 'qux'), $config->getTrustedUsers());
    }

    public function testLoadWithWrongData()
    {
        $repository = new UserConfigurationRepository(__DIR__.'/../Fixtures/config-empty.yml');
        $config = $repository->load();

        $this->assertInstanceOf('SensioLabs\Melody\Configuration\UserConfiguration', $config);
        $this->assertEmpty($config->getTrustedSignatures());
        $this->assertEmpty($config->getTrustedUsers());
    }

    public function testLoadWithoutPreviousData()
    {
        $repository = new UserConfigurationRepository(__DIR__.'/../Fixtures/does_not_exists.yml');
        $config = $repository->load();

        $this->assertInstanceOf('SensioLabs\Melody\Configuration\UserConfiguration', $config);
        $this->assertEmpty($config->getTrustedSignatures());
        $this->assertEmpty($config->getTrustedUsers());
    }

    /**
     * @expectedException \SensioLabs\Melody\Exception\ConfigException
     */
    public function testLoadWithoutWrongData()
    {
        $filename = sprintf('%s/%s', sys_get_temp_dir(), uniqid());
        if (file_exists($filename)) {
            unlink($filename);
        }

        file_put_contents($filename, 'foo: *bar');

        try {
            $repository = new UserConfigurationRepository($filename);
            $repository->load();
            unlink($filename);
        } catch (\Exception $e) {
            unlink($filename);
            throw $e;
        }
    }

    public function testSave()
    {
        $filename = sprintf('%s/%s', sys_get_temp_dir(), uniqid());
        if (file_exists($filename)) {
            unlink($filename);
        }
        $config = new UserConfiguration();
        $config->addTrustedSignatures(array('foo', 'bar'));
        $config->addTrustedUsers(array('baz', 'qux'));

        $repository = new UserConfigurationRepository($filename);
        $repository->save($config);

        $expected = 'trust:
  signatures:
    - foo
    - bar
  users:
    - baz
    - qux
';

        $this->assertFileExists($filename);
        $this->assertEquals($expected, file_get_contents($filename));
        unlink($filename);
    }
}
