<?php

namespace SensioLabs\Melody\Tests\Configuration;

use SensioLabs\Melody\Configuration\UserConfiguration;
use SensioLabs\Melody\Configuration\UserConfigurationRepository;
use SensioLabs\Melody\Security\TokenStorage;

class UserConfigurationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $repositpory = new UserConfigurationRepository(__DIR__.'/../Fixtures/config.yml');
        $config = $repositpory->load();

        $this->assertInstanceOf('SensioLabs\Melody\Configuration\UserConfiguration', $config);
        $this->assertEquals(array('foo', 'bar'), $config->getTrustedSignatures());
        $this->assertEquals(array('baz', 'qux'), $config->getTrustedUsers());
    }

    public function testLoadWithWrongData()
    {
        $repositpory = new UserConfigurationRepository(__DIR__.'/../Fixtures/config-empty.yml');
        $config = $repositpory->load();

        $this->assertInstanceOf('SensioLabs\Melody\Configuration\UserConfiguration', $config);
        $this->assertEmpty($config->getTrustedSignatures());
        $this->assertEmpty($config->getTrustedUsers());
    }

    public function testLoadWithoutPreviousData()
    {
        $repositpory = new UserConfigurationRepository(__DIR__.'/../Fixtures/does_not_exists.yml');
        $config = $repositpory->load();

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
            $repositpory = new UserConfigurationRepository($filename);
            $repositpory->load();
            unlink($filename);
        } catch(\Exception $e) {
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
        $authenticationStorage = new TokenStorage();
        $config = new UserConfiguration($authenticationStorage);
        $config->addTrustedSignatures(array('foo', 'bar'));
        $config->addTrustedUsers(array('baz', 'qux'));
        $authenticationStorage->set('foo', array('bar' => 'baz'));

        $repositpory = new UserConfigurationRepository($filename);
        $repositpory->save($config);

        $expected = 'trust:
  signatures:
    - foo
    - bar
  users:
    - baz
    - qux
authentication_data:
  foo:
    bar: baz
';

        $this->assertTrue(file_exists($filename));
        $this->assertEquals($expected, file_get_contents($filename));
        unlink($filename);
    }
}
