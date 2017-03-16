<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 08:45
 */
namespace Lch\MediaBundle\Listener;

use Lch\MediaBundle\Entity\Pdf;
use Lch\MediaBundle\Event\ListItemEvent;
use Lch\MediaBundle\Event\PrePersistEvent;
use Lch\MediaBundle\Event\ThumbnailEvent;
use Lch\MediaBundle\LchMediaEvents;
use Lch\MediaBundle\Manager\MediaManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PdfSubscriber implements EventSubscriberInterface
{
    /**
     * @var MediaManager
     */
    private $mediaManager;

    /**
     * PdfSubscriber constructor.
     * @param MediaManager $mediaManager
     */
    public function __construct(MediaManager $mediaManager) {
        $this->mediaManager = $mediaManager;
    }


    public static function getSubscribedEvents() {
        return [
            LchMediaEvents::PRE_PERSIST => 'onPdfPrePersist',
            LchMediaEvents::THUMBNAIL => 'onPdfThumbnail',
            LchMediaEvents::LIST_ITEM => 'onPdfListItem'
        ];
    }
    
    /**
     * @param PrePersistEvent $event
     */
    public function onPdfPrePersist(PrePersistEvent $event) {
        
        $pdf = $event->getMedia();

        // Only for images
        if(!$pdf instanceof Pdf) {
            return;
        }

        if (null !== $pdf->getFile()) {
            
            // TODO add checks, see how to pass constraints to event?


            $fileName = $this->mediaManager->upload($pdf);
            $pdf->setFile($fileName);

            $event->setMedia($pdf);
            $event->setData([
                'id'        => $pdf->getId(),
                'name'      => $pdf->getName(),
                'url'       => $pdf->getFile(),
            ]);
        }
    }

    /**
     * @param ThumbnailEvent $event
     */
    public function onPdfThumbnail(ThumbnailEvent $event) {
        $pdf = $event->getMedia();

        // Only for PDF
        if(!$pdf instanceof Pdf) {
            return;
        }
        // TODO elaborate
//        $event->setThumbnailPath($image->getFile());
    }

    /**
     * @param ListItemEvent $event
     */
    public function onPdfListItem(ListItemEvent $event) {
        $pdf = $event->getMedia();

        // Only for images
        if(!$pdf instanceof Pdf) {
            return;
        }
    }
}