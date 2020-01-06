<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 06/04/17
 * Time: 11:21
 */

namespace Lch\MediaBundle\Event;


use Lch\MediaBundle\Entity\Media;
use Symfony\Contracts\EventDispatcher\Event;

class PostDeleteEvent extends Event
{
    /**
     * @var Media
     */
    private $media;

    public function __construct(Media $media) {
        $this->media = $media;
    }

    /**
     * @return Media
     */
    public function getMedia(): Media
    {
        return $this->media;
    }

    /**
     * @param Media $media
     * @return PostDeleteEvent
     */
    public function setMedia(Media $media): PostDeleteEvent
    {
        $this->media = $media;
        return $this;
    }
}