<?php

namespace SensioLabs\Melody\Handler;

use SensioLabs\Melody\Resource\LocalResource;

/**
 * Class FileHandler.
 *
 * @author Charles Sarrazin <charles@sarraz.in>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class FileHandler implements ResourceHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($filename)
    {
        return is_file($filename) && is_readable($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function createResource($filename)
    {
        return new LocalResource($filename, file_get_contents($filename));
    }
}
