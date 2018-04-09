<?php

namespace SensioLabs\Melody\Tests\Handler;

use SensioLabs\Melody\Handler\GistHandler;
use SensioLabs\Melody\Security\TokenStorage;

class GistHandlerTest extends \PHPUnit_Framework_TestCase
{
    const SINGLE_URL = 'https://gist.github.com/lyrixx/44c737e5096e69ea9305';
    const MULTI_URL = 'https://gist.github.com/alexandresalome/e1dcf99dedadd4838a99';

    private $handler;

    protected function setUp()
    {
        $this->handler = new GistHandler(new TokenStorage());
    }

    protected function tearDown()
    {
        $this->handler = null;
    }

    public function provideSupports()
    {
        return array(
            array(__DIR__.'/../fixtures/foo.php', false),
            array(__DIR__.'/../fixtures/foobar.php', false),
            array(__DIR__.'/../fixtures', false),
            array('https://gist.github.com/foobar/7494d27255d0561157b8', true),
            array('https://gist.github.com/foobar/7494d27255d0561157b8', true),
            array('https://gist.github.com/foobar-/7494d27255d0561157b8', true),
            array('https://gist.github.com/foo-bar/7494d27255d0561157b8', true),
            array('https://gist.github.com/-foobar/7494d27255d0561157b8', false),
            array('https://gist.github.com/thisusernameistoolongandshouldnotbevalid/7494d27255d0561157b8', false),
            array('https://gist.github.com/thisusernameisnttoolongandshouldbevalid/7494d27255d0561157b8', true),
            array('foobar/7494d27255d0561157b8', true),
            array('foobar-/7494d27255d0561157b8', true),
            array('foo-bar/7494d27255d0561157b8', true),
            array('-foobar/7494d27255d0561157b8', false),
            array('thisusernameistoolongandshouldnotbevalid/7494d27255d0561157b8', false),
            array('thisusernameisnttoolongandshouldbevalid/7494d27255d0561157b8', true),
        );
    }

    /**
     * @dataProvider provideSupports
     */
    public function testSupports($url, $expected)
    {
        $this->assertEquals($expected, $this->handler->supports($url));
    }

    public function testCreateResource()
    {
        $resource = $this->handler->createResource(self::SINGLE_URL);

        $this->assertInstanceOf('SensioLabs\Melody\Resource\Resource', $resource);
        $this->assertContains('symfony/console', $resource->getContent());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateResourceWithMultipleFileInGist()
    {
        $this->handler->createResource(self::MULTI_URL);
    }
}
