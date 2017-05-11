<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 06/04/17
 * Time: 11:30
 */

namespace Lch\MediaBundle\Listener;


use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Entity\Pdf;
use Lch\MediaBundle\Event\PostDeleteEvent;
use Lch\MediaBundle\Event\PostStorageEvent;
use Lch\MediaBundle\Event\ThumbnailEvent;
use Lch\MediaBundle\LchMediaEvents;
use Lch\MediaBundle\Loader\MediaUploader;
use Lch\MediaBundle\Manager\MediaManager;
use Lch\MediaBundle\Manager\PdfManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class MediaSubscriber implements EventSubscriberInterface
{

    /**
     * @var MediaUploader $mediaUploader
     */
    private $mediaUploader;

    /**
     * @var string $kernelRootDir
     */
    private $kernelRootDir;

    /**
     * @var PdfManager $pdfManager
     */
    private $pdfManager;

    /**
     * MediaSubscriber constructor.
     * @param MediaUploader $mediaUploader
     * @param String $kernelRootDir
     */
    public function __construct(MediaUploader $mediaUploader, PdfManager $pdfManager, string $kernelRootDir) {
        $this->mediaUploader = $mediaUploader;
        $this->kernelRootDir = $kernelRootDir;
        $this->pdfManager = $pdfManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents() {
        return [
            LchMediaEvents::POST_DELETE => 'onMediaPostDelete',
            LchMediaEvents::THUMBNAIL => 'onMediaThumbnail',
            LchMediaEvents::POST_STORAGE => 'onMediaPostStorage'
        ];
    }

    /**
     * @param PostDeleteEvent $postDeleteEvent
     */
    public function onMediaPostDelete(PostDeleteEvent $postDeleteEvent) {
        $media = $postDeleteEvent->getMedia();

        // Only for media physically stored
        if (!$media instanceof Media || !$this->mediaUploader->checkStorable($media)) {
            return;
        }

        $file = $media->getFile();

        if(!($file instanceof File)) {
            throw new Exception('File is not File instance');
        }

        $fs = new Filesystem();

        // TODO adapt with thumbnails
        $fs->remove($file->getPathname());
    }

    /**
     * Launch thumbnail generationfor PDF
     * @param ThumbnailEvent $thumbnailEvent
     */
    public function onMediaThumbnail(ThumbnailEvent $thumbnailEvent){

        $media = $thumbnailEvent->getMedia();

        if ($media instanceof Media && (
                $media->getFile()->getExtension() == 'pdf'
                ||
                $media->getFile()->getExtension() == 'PDF')
        ){

//            $this->pdfManager->generateThumbnail($media);
            if(isset($thumbnailEvent->getThumbnailParameters()['size'])) {
                $size = $thumbnailEvent->getThumbnailParameters()['size'];
            } else {
                $size = 'thumbnail';
            }

            if ("" !== ($mediaThumbnail = $this->pdfManager->getThumbnailUrl($media, $size))){
                $thumbnailEvent->setThumbnailPath($mediaThumbnail);
            }
        }
    }

    public function onMediaPostStorage(PostStorageEvent $event) {
        $media = $event->getMedia();

        if ($media instanceof Media && (
                $media->getFile()->getExtension() == 'pdf'
                ||
                $media->getFile()->getExtension() == 'PDF')
        ){
            $this->pdfManager->generateThumbnail($media);
        }
    }
}