<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 08:40
 */

namespace Lch\MediaBundle\Event;


use Lch\MediaBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;

class PostPersistEvent extends Event
{
    /**
     * @var Media
     */
    private $media;

    /**
     * PostPersistEvent constructor.
     * @param Media $media
     */
    public function __construct(Media $media) {
        $this->media = $media;
    }
    
    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param Media $media
     * @return PrePersistEvent
     */
    public function setMedia($media)
    {
        $this->media = $media;
        return $this;
    }

}