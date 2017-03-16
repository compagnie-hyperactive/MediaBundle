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

class StorageEvent extends Event
{
    use Mediable;

    /**
     * @var string
     */
    private $relativeFilePath;
    /**
     * @var string
     */
    private $fileName;

    /**
     * StorageEvent constructor.
     * @param $relativeFilePath
     * @param $fileName
     */
    public function __construct(Media $media, $relativeFilePath, $fileName) {
        $this->media = $media;
        $this->relativeFilePath = $relativeFilePath;
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getRelativeFilePath(): string
    {
        return $this->relativeFilePath;
    }

    /**
     * @param string $relativeFilePath
     * @return StorageEvent
     */
    public function setRelativeFilePath(string $relativeFilePath): StorageEvent
    {
        $this->relativeFilePath = $relativeFilePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return StorageEvent
     */
    public function setFileName(string $fileName): StorageEvent
    {
        $this->fileName = $fileName;
        return $this;
    }

}