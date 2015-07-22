<?php

namespace SensioLabs\Melody\Tests\Handler;

use SensioLabs\Melody\Handler\FileHandler;

class FileHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $handler;

    protected function setUp()
    {
        $this->handler = new FileHandler();
    }

    protected function tearDown()
    {
        $this->handler = null;
    }

    public function provideSupports()
    {
        return array(
            array(__DIR__.'/../Integration/hello-world.php', true),
            array(__DIR__.'/../Integration/Path/To/InvalidFile.php', false),
            array(__DIR__.'/../Integration', false),
            array('https://gist.github.com/csarrazi/7494d27255d0561157b8', false),
        );
    }

    /**
     * @dataProvider provideSupports
     */
    public function testSupports($path, $expected)
    {
        $this->assertEquals($expected, $this->handler->supports($path));
    }

    public function testCreateResource()
    {
        $filename = __DIR__.'/../Integration/hello-world.php';

        $resource = $this->handler->createResource($filename);

        $this->assertInstanceOf('SensioLabs\Melody\Resource\Resource', $resource);
        $this->assertSame(file_get_contents($filename), $resource->getContent());
    }
}
