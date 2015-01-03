<?php

namespace SensioLabs\Melody\Tests\WorkingDirectory;

use SensioLabs\Melody\WorkingDirectory\WorkingDirectoryFactory;
use org\bovigo\vfs\vfsStream;

class WorkingDirectoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $storageDir;
    /** @var  \SensioLabs\Melody\WorkingDirectory\WorkingDirectoryFactory */
    private $workingDirectoryFactory;

    protected function setUp()
    {
        $this->storageDir = vfsStream::setup('storageDir');
        $this->workingDirectoryFactory = new WorkingDirectoryFactory($this->storageDir->url());
    }

    public function testCreateTmpDir()
    {
        $workingDirectory = $this->workingDirectoryFactory->createTmpDir(array(), array());

        $workingDirectoryPath = $workingDirectory->getPath();
        $this->assertNotEquals($this->storageDir->url(), $workingDirectoryPath);
        $this->assertEquals($this->storageDir->url(), dirname($workingDirectoryPath));
        $this->assertNotEmpty(basename($workingDirectoryPath));
    }

    public static function sameConfigProvider()
    {
        return array(
            array(
                array(), array(),
                array(), array()
            ),
            array(
                array('symfony/symfony' => '*'), array(),
                array('symfony/symfony' => '*'), array()
            ),
            array(
                array(
                    'symfony/symfony' => '*',
                    'sensiolabs/melody' => '*',
                ),
                array(),
                array(
                    'sensiolabs/melody' => '*',
                    'symfony/symfony' => '*',
                ),
                array()
            ),
        );
    }

    /**
     * @dataProvider sameConfigProvider
     */
    public function testCreateTmpDirWithSameConfigShouldNotChange($packages, $repositories, $otherPackages, $otherRepositories)
    {
        $this->assertEquals(
            $this->workingDirectoryFactory->createTmpDir($packages, $repositories)->getPath(),
            $this->workingDirectoryFactory->createTmpDir($otherPackages, $otherRepositories)->getPath()
        );
    }

    public static function differentConfigProvider()
    {
        return array(
            array(
                array(), array(),
                array('symfony/symfony' => '*'), array()
            ),
            array(
                array('sensiolabs/melody' => '*'),
                array(),
                array('sensiolabs/melody' => '*'),
                array(
                    array(
                        'type' => 'vcs',
                        'url' => 'https://example.com/melody',
                    )
                )
            ),
            array(
                array('sensiolabs/melody' => '*'),
                array(
                    array(
                        'type' => 'vcs',
                        'url' => 'https://example.com/melody',
                    )
                ),
                array('sensiolabs/melody' => '*'),
                array(
                    array(
                        'type' => 'vcs',
                        'url' => 'https://otherexample.com/melody',
                    )
                )
            ),
        );
    }

    /**
     * @dataProvider differentConfigProvider
     */
    public function testCreateTmpDirWithDifferentConfigShouldChange($packages, $repositories, $otherPackages, $otherRepositories)
    {
        $this->assertNotEquals(
            $this->workingDirectoryFactory->createTmpDir($packages, $repositories)->getPath(),
            $this->workingDirectoryFactory->createTmpDir($otherPackages, $otherRepositories)->getPath()
        );
    }
}
