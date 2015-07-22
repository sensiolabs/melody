<?php

namespace SensioLabs\Melody\Handler;

use SensioLabs\Melody\Resource\Resource;

/**
 * Interface ResourceHandlerInterface.
 *
 * @author Charles Sarrazin <charles@sarraz.in>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
interface ResourceHandlerInterface
{
    /**
     * Returns whether the filename is supported or not by the current handler.
     *
     * @param string $filename
     *
     * @return bool
     */
    public function supports($filename);

    /**
     * Creates a new resources, based on a filename.
     *
     * @param string $filename
     *
     * @return Resource
     */
    public function createResource($filename);
}
