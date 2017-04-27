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
     * @var string
     */
    private $definitiveFilePath;

    /**
     * StorageEvent constructor.
     * @param string $definitiveFilePath
     * @param Media $media
     */
    public function __construct(Media $media, $definitiveFilePath) {
        $this->media = $media;
        $this->definitiveFilePath = $definitiveFilePath;
        $this->media = $media;
    }

    /**
     * @return string
     */
    public function getDefinitiveFilePath()
    {
        return $this->definitiveFilePath;
    }

    /**
     * @param string $definitiveFilePath
     * @return PostStorageEvent
     */
    public function setDefinitiveFilePath($definitiveFilePath)
    {
        $this->definitiveFilePath = $definitiveFilePath;
        return $this;
    }
}