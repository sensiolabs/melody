<?php

namespace SensioLabs\Melody\Resource;

/**
 * Resource.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class Resource
{
    private $content;
    private $metadata;

    public function __construct($content, Metadata $metadata = null)
    {
        $this->content = $content;
        $this->metadata = $metadata ?: new Metadata();
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function getSignature()
    {
        return hash('sha512', $this->content);
    }
}
