<?php

namespace SensioLabs\Melody\Tests\Handler\Github;

use SensioLabs\Melody\Handler\Github\Gist;

class GistTest extends \PHPUnit_Framework_TestCase
{
    public function provideInvalidUrl()
    {
        return array(
            array(''),
            array('http://foo'),
            array('https://foo/bar'),
            array('http://gist.foo.bar/bar/123')
        );
    }

    /**
     * @dataProvider      provideInvalidUrl
     * @expectedException InvalidArgumentException
     */
    public function testInvalidUrl($invalidUrl)
    {
        $gist = new Gist($invalidUrl);
    }
}
