<?php

namespace SensioLabs\Melody\Resource;

/**
 * Metadata.
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class Metadata
{
    private $id;
    private $owner;
    private $createdAd;
    private $updatedAt;
    private $revision;
    private $uri;

    public function __construct($id = null, $owner = null, \DateTime $createdAd = null, \DateTime $updatedAt = null, $revision = null, $uri = null)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->createdAd = $createdAd;
        $this->updatedAt = $updatedAt;
        $this->revision = $revision;
        $this->uri = $uri;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function getCreatedAt()
    {
        return $this->createdAd;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getRevision()
    {
        return $this->revision;
    }

    public function getUri()
    {
        return $this->uri;
    }
}
