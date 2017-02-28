<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 16:35
 */

namespace Lch\MediaBundle\Event;


use Lch\MediaBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;

class ThumbnailEvent extends Event implements MediaTemplateEventInterface
{
    /**
     * @var Media
     */
    private $media;

    /**
     * @var string 
     */
    private $template;

    /**
     * @var array
     */
    private $thumbnailParameters;

    /**
     * @return string
     */
    public function getThumbnailPath()
    {
        return $this->thumbnailPath;
    }

    /**
     * @param string $thumbnailPath
     * @return ThumbnailEvent
     */
    public function setThumbnailPath($thumbnailPath)
    {
        $this->thumbnailPath = $thumbnailPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template) {
        $this->template = $template;
        return $this;
    }

    /**
     * @var string
     */
    private $thumbnailPath;

    /**
     * ThumbnailEvent constructor.
     * @param Media $media
     */
    public function __construct(Media $media) {
        $this->media = $media;
    }

    /**
     * @return array
     */
    public function getThumbnailParameters()
    {
        return $this->thumbnailParameters;
    }

    /**
     * @param array $thumbnailParameters
     * @return ThumbnailEvent
     */
    public function setThumbnailParameters($thumbnailParameters)
    {
        $this->thumbnailParameters = $thumbnailParameters;
        return $this;
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
     * @return ThumbnailEvent
     */
    public function setMedia($media)
    {
        $this->media = $media;
        return $this;
    }

}