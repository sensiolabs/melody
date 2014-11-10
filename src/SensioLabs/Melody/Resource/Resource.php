<?php

namespace SensioLabs\Melody\Resource;

/**
 * Resource
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class Resource
{
    private $filename;
    private $content;
    private $local;

    public function __construct($filename, $content, $local)
    {
        $this->filename = $filename;
        $this->content = $content;
        $this->local = $local;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function isLocal()
    {
        return $this->local;
    }
}
