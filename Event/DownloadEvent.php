<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 16/03/17
 * Time: 16:38
 */

namespace Lch\MediaBundle\Event;


use Lch\MediaBundle\Behavior\Mediable;
use Lch\MediaBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\File\File;

class DownloadEvent extends Event
{
    use Mediable;

    /**
     * @var File
     */
    private $file;


    /**
     * DownloadEvent constructor.
     * @param Media $media
     * @param File $file
     */
    public function __construct(Media $media, File $file) {
        $this->media = $media;
        $this->file = $file;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     * @return DownloadEvent
     */
    public function setFile(File $file): DownloadEvent
    {
        $this->file = $file;
        return $this;
    }

}