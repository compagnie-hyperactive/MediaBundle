<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 16/03/17
 * Time: 15:03
 */

namespace Lch\MediaBundle\Behavior;


use Lch\MediaBundle\Entity\Media;

trait Mediable
{
    /**
     * @var Media
     */
    private $media;

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param Media $media
     * @return $this
     */
    public function setMedia($media)
    {
        $this->media = $media;
        return $this;
    }
}