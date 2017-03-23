<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 16:29
 */

namespace Lch\MediaBundle\Listener;


use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Lch\MediaBundle\Entity\Image;
use Doctrine\ORM\Events as DoctrineEvents;
use Lch\MediaBundle\Event\ListItemEvent;
use Lch\MediaBundle\Event\PrePersistEvent;
use Lch\MediaBundle\Event\ThumbnailEvent;
use Lch\MediaBundle\LchMediaEvents;
use Lch\MediaBundle\Manager\MediaManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;

class ImageSubscriber implements EventSubscriberInterface
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


    public static function getSubscribedEvents() {
        return [
            LchMediaEvents::PRE_PERSIST => 'onImagePrePersist',
            LchMediaEvents::THUMBNAIL => 'onImageThumbnail',
            LchMediaEvents::LIST_ITEM => 'onImageListItem'
        ];
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

            $fileName = $this->mediaManager->upload($image);
            $image->setFile($fileName);

            $event->setMedia($image);
            $event->setData([
                'name'      => $image->getName(),
                'url'       => $this->mediaManager->getRealRelativeUrl($image->getFile()),
                // TODO find a way to trigger thumbnail generation from here
//                'thumbnail' => '<img width="50" src="' . $this->getRelativeUrl($image->getFile()) . '" />'
            ]);
        }
    }

    /**
     * @param ThumbnailEvent $event
     */
    public function onImageThumbnail(ThumbnailEvent $event) {
        $image = $event->getMedia();

        // Only for images
        if(!$image instanceof Image) {
            return;
        }
        // TODO elaborate
        $event->setThumbnailPath($this->mediaManager->getRealRelativeUrl($image->getFile()));
    }

    /**
     * @param ListItemEvent $event
     */
    public function onImageListItem(ListItemEvent $event) {
        $image = $event->getMedia();

        // Only for images
        if(!$image instanceof Image) {
            return;
        }
    }
}