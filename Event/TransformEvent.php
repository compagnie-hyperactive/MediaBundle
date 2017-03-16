<?php

namespace Lch\MediaBundle\Event;

use Lch\MediaBundle\Behavior\Mediable;
use Lch\MediaBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ImageEvent
 * @package Lch\MediaBundle\Event
 */
class TransformEvent extends Event implements MediaTemplateEventInterface
{
    use Mediable;

    /** @var  array */
    private $mediaParameters;
    /**
     * @var string
     */
    private $template;

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
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @return TransformEvent
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
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