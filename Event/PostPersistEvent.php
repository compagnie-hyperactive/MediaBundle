<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 08:40
 */

namespace Lch\MediaBundle\Event;


use Lch\MediaBundle\Behavior\Mediable;
use Lch\MediaBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;

class PostPersistEvent extends Event
{
    use Mediable;

    /**
     * PostPersistEvent constructor.
     * @param Media $media
     */
    public function __construct(Media $media) {
        $this->media = $media;
    }
}