<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 16/03/17
 * Time: 14:13
 */

namespace Lch\MediaBundle\Event;


use Lch\MediaBundle\Behavior\Mediable;
use Lch\MediaBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;

class PostStorageEvent extends Event
{
    use Mediable;

    /**
     * StorageEvent constructor.
     * @param Media $media
     */
    public function __construct(Media $media) {
        $this->media = $media;
    }
}