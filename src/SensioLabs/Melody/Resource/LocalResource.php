<?php

namespace SensioLabs\Melody\Resource;

/**
 * LocalResource.
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class LocalResource extends Resource
{
    private $filename;

    public function __construct($filename, $content)
    {
        parent::__construct($content);
        $this->filename = $filename;
    }

    public function getFilename()
    {
        return $this->filename;
    }
}
