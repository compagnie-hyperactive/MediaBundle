<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/03/17
 * Time: 15:41
 */

namespace Lch\MediaBundle\Event;


use Lch\MediaBundle\Entity\Media;
use Symfony\Component\EventDispatcher\Event;

class UrlEvent extends Event
{

    /**
     * @var Media
     */
    private $media;

    /**
     * @var string
     */
    private $url;

    /**
     * UrlEvent constructor.
     * @param Media $media
     * @param string $url
     */
    public function __construct(Media $media, string $url) {
        $this->media = $media;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return UrlEvent
     */
    public function setUrl(string $url): UrlEvent
    {
        $this->url = $url;
        return $this;
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
     * @return UrlEvent
     */
    public function setMedia(Media $media): UrlEvent
    {
        $this->media = $media;
        return $this;
    }
}