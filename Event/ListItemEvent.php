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

class ListItemEvent extends Event implements MediaTemplateEventInterface
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
     * @return Media
     */
    public function getMedia() {
        return $this->media;
    }

    /**
     * @param Media $media
     * @return ThumbnailEvent
     */
    public function setMedia($media) {
        $this->media = $media;
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
}