<?php

namespace Lch\MediaBundle\Manager;

use Doctrine\ORM\EntityManager;
use Lch\MediaBundle\DependencyInjection\Configuration;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Event\ListItemEvent;
use Lch\MediaBundle\Event\MediaTemplateEventInterface;
use Lch\MediaBundle\Event\ThumbnailEvent;
use Lch\MediaBundle\LchMediaEvents;
use Lch\MediaBundle\Model\ImageInterface;
use Lch\MediaBundle\Uploader\MediaUploader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaManager
{
    /**
     * @var MediaUploader $mediaUploader
     */
    private $mediaUploader;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $mediaTypes;

    /**
     * MediaManager constructor.
     * @param MediaUploader $mediaUploader
     * @param EntityManager $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param array $mediaTypes
     */
    public function __construct(MediaUploader $mediaUploader, EntityManager $entityManager, EventDispatcherInterface $eventDispatcher, array $mediaTypes)
    {
        $this->mediaUploader = $mediaUploader;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->mediaTypes = $mediaTypes;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file)
    {
        $fileName = $this->mediaUploader->upload($file);

        return $fileName;
    }

    /**
     * @param Media $media
     * @return MediaTemplateEventInterface
     */
    public function getThumbnail(Media $media) {
        $thumbnailEvent = new ThumbnailEvent($media);
//        $thumbnailEvent->setTemplate($this->getMediaTypeConfiguration($media)[Configuration::LIST_ITEM_VIEW]);
        $thumbnailEvent->setMedia($media);
        $thumbnailEvent->setTemplate($this->getMediaTypeConfiguration($media)[Configuration::THUMBNAIL_VIEW]);

        $this->eventDispatcher->dispatch(
            LchMediaEvents::THUMBNAIL,
            $thumbnailEvent
        );
        
        // TODO: add one by default

        return $thumbnailEvent;
    }

    /**
     * @param Media $media
     * @return MediaTemplateEventInterface
     * @throws \Exception
     */
    public function getListItem(Media $media) {
        $listItemEvent = new ListItemEvent($media);
        $listItemEvent->setMedia($media);
        $listItemEvent->setTemplate($this->getMediaTypeConfiguration($media)[Configuration::LIST_ITEM_VIEW]);

        $this->eventDispatcher->dispatch(
            LchMediaEvents::LIST_ITEM,
            $listItemEvent
        );

        // TODO: add one by default

        return $listItemEvent;
    }

    /**
     * @param array $authorizedMediasTypes
     * @return array
     */
    public function getFilteredMedias(array $authorizedMediasTypes) {
        $authorizedMediasQueryBuilder = $this->entityManager->createQueryBuilder();

        $medias = [];
        
        foreach($authorizedMediasTypes as $alias => $authorizedMediasType) {
            $authorizedMediasQueryBuilder
                ->select($alias)
                ->from($authorizedMediasType[Configuration::ENTITY], $alias)
            ;
            // TODO order, add tags for filtering? 
            $medias = array_merge($medias, $authorizedMediasQueryBuilder->getQuery()->getResult());
        }
        return $medias;
    }

    /**
     * @param Media $media
     * @return array
     * @throws \Exception
     */
    public function getMediaTypeConfiguration(Media $media) {
        foreach($this->mediaTypes as $mediaType) {
            // TODO find wwwwayyy better (due to Proxy class)
            if(strpos(get_class($media), $mediaType[Configuration::ENTITY]) !== false) {
                return $mediaType;
            }
        }
        throw new \Exception();
    }
}