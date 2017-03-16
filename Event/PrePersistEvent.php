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

class PrePersistEvent extends Event
{
    use Mediable;

    /**
     * @var array $data array containing media information for form type display
     */
    private $data;

    public function __construct(Media $media) {
        $this->media = $media;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return PrePersistEvent
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}