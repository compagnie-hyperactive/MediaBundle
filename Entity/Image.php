<?php

namespace Lch\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lch\MediaBundle\Behavior\Storable;
use Lch\MediaBundle\Validator\Constraints\ImageSize;

/**
 * Class Image
 * @package Lch\MediaBundle\Entity
 * @ORM\MappedSuperclass()
 */
abstract class Image extends Media
{
    use Storable;

    const SIZE = 'size';
    
    /**
     * @var string $alt the alternative text
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    protected $alt;

    /**
     * @var integer $width image main file's width
     *
     * @ORM\Column(name="width", type="integer")
     */
    protected $width;

    /**
     * @var integer $height image main file's height
     *
     * @ORM\Column(name="height", type="integer")
     */
    protected $height;

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @inheritdoc
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }
}