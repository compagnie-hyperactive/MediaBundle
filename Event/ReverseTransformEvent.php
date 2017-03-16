<?php

namespace Lch\MediaBundle\Event;

use Lch\MediaBundle\Behavior\Mediable;
use Lch\MediaBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ImageEvent
 * @package Lch\MediaBundle\Event
 */
class ReverseTransformEvent extends Event
{
    use Mediable;

    /** @var  array */
    private $mediaParameters;

    /**
     * ImageEvent constructor.
     * @param Media $media
     * @param array $imagesParameters
     */
    public function __construct(Media $media, array $imagesParameters)
    {
        $this->media = $media;
        $this->mediaParameters = $imagesParameters;
    }

    /**
     * @return array
     */
    public function getMediaParameters()
    {
        return $this->mediaParameters;
    }

    /**
     * @param array $mediaParameters
     * @return $this
     */
    public function setMediaParameters(array $mediaParameters)
    {
        $this->mediaParameters = $mediaParameters;

        return $this;
    }
}