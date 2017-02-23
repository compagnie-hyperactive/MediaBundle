<?php

namespace Lch\MediaBundle\Event;

use Lch\MediaBundle\Entity\Image;
use Lch\MediaBundle\Model\ImageInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ImageEvent
 * @package Lch\MediaBundle\Event
 */
class ImageEvent extends Event
{
    /** @var ImageInterface */
    private $image;

    /** @var  array */
    private $imageParam;

    /**
     * ImageEvent constructor.
     * @param Image $image
     * @param array $imageParam
     */
    public function __construct(Image $image, array $imageParam)
    {
        $this->image = $image;
        $this->imageParam = $imageParam;
    }

    /**
     * @param Image $image
     * @return $this
     */
    public function setImage(Image $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return ImageInterface
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return array
     */
    public function getImageParam()
    {
        return $this->imageParam;
    }

    /**
     * @param array $imageParam
     * @return $this
     */
    public function setImageParam(array $imageParam)
    {
        $this->imageParam = $imageParam;

        return $this;
    }
}