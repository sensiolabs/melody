<?php

namespace SensioLabs\Melody\Handler;

use SensioLabs\Melody\Resource\Resource;

/**
 * Class StreamHandler
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class StreamHandler implements ResourceHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($filename)
    {
        $opened = @fopen($filename, 'r');

        return false !== $opened;
    }

    /**
     * {@inheritdoc}
     */
    public function createResource($filename)
    {
        return new Resource(file_get_contents($filename));
    }
}
