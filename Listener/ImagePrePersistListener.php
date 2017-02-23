<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 08:45
 */
namespace Lch\MediaBundle\Listener;

use Lch\MediaBundle\Entity\Image;
use Lch\MediaBundle\Event\PrePersistEvent;
use Lch\MediaBundle\Manager\MediaManager;

class ImagePrePersistListener
{
    /**
     * @var MediaManager
     */
    private $mediaManager;

    /**
     * ImagePrePersistListener constructor.
     * @param MediaManager $mediaManager
     */
    public function __construct(MediaManager $mediaManager) {
        $this->mediaManager = $mediaManager;
    }

    /**
     * @param PrePersistEvent $event
     */
    public function onImagePrePersist(PrePersistEvent $event) {
        
        $image = $event->getMedia();

        // Only for images
        if(!$image instanceof Image) {
            return;
        }

        if (null !== $image->getFile()) {

            $imageInfos = getimagesize($image->getFile());

            // TODO add checks, see how to pass constraints to event?
            $image->setWidth($imageInfos[0]);
            $image->setHeight($imageInfos[1]);

            if(empty($image->getName())) {
                $image->setName(pathinfo($image->getFile()->getClientOriginalName(), PATHINFO_BASENAME));
            }
            if(empty($image->getAlt())) {
                $image->setAlt(pathinfo($image->getFile(), PATHINFO_BASENAME));
            }

            $fileName = $this->mediaManager->upload($image->getFile());
            $image->setFile($fileName);

            $event->setMedia($image);
            $event->setData([
                'id'        => $image->getId(),
                'name'      => $image->getName(),
                'url'       => $image->getFile(),
            ]);
        }
    }
}