<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 08:45
 */
namespace Lch\MediaBundle\Listener;

use Lch\MediaBundle\Entity\Pdf;
use Lch\MediaBundle\Event\PrePersistEvent;
use Lch\MediaBundle\Manager\MediaManager;

class PdfPrePersistListener
{
    /**
     * @var MediaManager
     */
    private $mediaManager;

    /**
     * PdfPrePersistListener constructor.
     * @param MediaManager $mediaManager
     */
    public function __construct(MediaManager $mediaManager) {
        $this->mediaManager = $mediaManager;
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


            $fileName = $this->mediaManager->upload($pdf->getFile());
            $pdf->setFile($fileName);

            $event->setMedia($pdf);
            $event->setData([
                'id'        => $pdf->getId(),
                'name'      => $pdf->getName(),
                'url'       => $pdf->getFile(),
            ]);
        }
    }
}