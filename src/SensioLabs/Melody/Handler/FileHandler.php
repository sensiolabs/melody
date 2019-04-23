<?php

namespace SensioLabs\Melody\Handler;

use SensioLabs\Melody\Resource\LocalResource;
use SensioLabs\Melody\Resource\Metadata;

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
        $stat = stat($filename);
        $metadata = new Metadata(
            $stat['ino'],
            $stat['uid'],
            new \DateTime(date('c', $stat['ctime'])),
            new \DateTime(date('c', $stat['mtime'])),
            1,
            sprintf('file://%s', realpath($filename))
        );

        $content = file_get_contents($filename);
        if ('#!' === substr($content, 0, 2)) {
            $content = explode("\n", $content."\n", 2)[1];
        }

        return new LocalResource($filename, $content, $metadata);
    }
}
