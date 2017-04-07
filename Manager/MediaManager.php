<?php

namespace Lch\MediaBundle\Manager;

use Doctrine\ORM\EntityManager;
use Knp\DoctrineBehaviors\Reflection\ClassAnalyzer;
use Lch\MediaBundle\Behavior\Storable;
use Lch\MediaBundle\DependencyInjection\Configuration;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Event\ListItemEvent;
use Lch\MediaBundle\Event\MediaTemplateEventInterface;
use Lch\MediaBundle\Event\PostSearchEvent;
use Lch\MediaBundle\Event\PreSearchEvent;
use Lch\MediaBundle\Event\StorageEvent;
use Lch\MediaBundle\Event\ThumbnailEvent;
use Lch\MediaBundle\Event\UrlEvent;
use Lch\MediaBundle\LchMediaEvents;
use Lch\MediaBundle\Loader\MediaUploader;
use Lch\MediaBundle\Model\ImageInterface;
use Lch\MediaBundle\Service\MediaTools;
use Lch\MediaBundle\Twig\Extension\MediaExtension;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaManager
{
    /**
     * @var MediaUploader $mediaUploader
     */
    private $mediaUploader;

    /**
     * @var MediaTools $mediaTools
     */
    private $mediaTools;

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
     * @param MediaTools $mediaTools
     * @param EntityManager $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param array $mediaTypes
     */
    public function __construct(MediaUploader $mediaUploader, MediaTools $mediaTools, EntityManager $entityManager, EventDispatcherInterface $eventDispatcher, array $mediaTypes)
    {
        $this->mediaUploader = $mediaUploader;
        $this->mediaTools = $mediaTools;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->mediaTypes = $mediaTypes;
    }

    /**
     * @return MediaUploader
     */
    public function getMediaUploader(): MediaUploader
    {
        return $this->mediaUploader;
    }

    /**
     * @param Media $media
     * @return string
     * @throws \Exception
     */
    public function upload(Media $media)
    {
        if(!$this->mediaUploader->checkStorable($media)) {
            // TODO Specialize
            throw new \Exception();
        }

        // Create file path
        $relativeFilePath = "/" . date('Y') . "/" . date('m') . "/";

        // Create file name
        $fileName = md5(uniqid()).'.' . $media->getFile()->guessExtension();

        // Throw event to act on storage
        $storageEvent = new StorageEvent($media, $relativeFilePath, $fileName);

        $this->eventDispatcher->dispatch(LchMediaEvents::STORAGE, $storageEvent);

        return $this->mediaUploader->upload($media, $storageEvent->getRelativeFilePath(), $storageEvent->getFileName());
    }


    public function download(Media $media) {

    }

    /**
     * @param Media $media
     * @return string
     * @throws \Exception
     */
    public function getUrl(Media $media) {

        if(!$this->mediaUploader->checkStorable($media)) {
            // TODO Specialize
            throw new \Exception();
        }

        $urlEvent = new UrlEvent($media, $this->mediaTools->getRealRelativeUrl($media->getFile()));

        $this->eventDispatcher->dispatch(
            LchMediaEvents::URL,
            $urlEvent
        );

        return $urlEvent->getUrl();
    }

    /**
     * @param Media $media
     * @return MediaTemplateEventInterface
     */
    public function getThumbnail(Media $media) {
        $thumbnailEvent = new ThumbnailEvent($media);
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
     * @param array $parameters
     * @return array
     */
    public function getFilteredMedias(array $authorizedMediasTypes, array $parameters) {
        $authorizedMediasQueryBuilder = $this->entityManager->createQueryBuilder();

        $medias = [];

        foreach($authorizedMediasTypes as $alias => $authorizedMediasType) {

            // Pre search event
            $preSearchEvent = new PreSearchEvent($authorizedMediasQueryBuilder, $parameters);
            $this->eventDispatcher->dispatch(
                LchMediaEvents::PRE_SEARCH,
                $preSearchEvent
            );

            $authorizedMediasQueryBuilder
                ->select($alias)
                ->from($authorizedMediasType[Configuration::ENTITY], $alias)
                ->where('1=1')
            ;

            // Search on common media properties
            if(isset($parameters[Media::NAME])) {
                $authorizedMediasQueryBuilder
                    ->andWhere($authorizedMediasQueryBuilder->expr()->like("{$alias}.name", ':name'))
                    ->setParameter('name', "%{$parameters[Media::NAME]}%")
                ;
            }

            // Post search event
            $postSearchEvent = new PostSearchEvent($preSearchEvent->getQueryBuilder(), $parameters);
            $this->eventDispatcher->dispatch(
                LchMediaEvents::PRE_SEARCH,
                $postSearchEvent
            );

            $medias = array_merge($medias, $postSearchEvent->getQueryBuilder()->getQuery()->getResult());
        }
        return $medias;
    }
}