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
use Lch\MediaBundle\Event\PostStorageEvent;
use Lch\MediaBundle\Event\PreSearchEvent;
use Lch\MediaBundle\Event\SearchFormEvent;
use Lch\MediaBundle\Event\PreStorageEvent;
use Lch\MediaBundle\Event\ThumbnailEvent;
use Lch\MediaBundle\Event\UrlEvent;
use Lch\MediaBundle\LchMediaEvents;
use Lch\MediaBundle\Loader\MediaUploader;
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
        $fileName = md5(uniqid()).'.' . $media->getFile()->getClientOriginalExtension();

        // Throw event to act before storage
        $preStorageEvent = new PreStorageEvent($media, $relativeFilePath, $fileName);
        $this->eventDispatcher->dispatch(
            $preStorageEvent,
            LchMediaEvents::PRE_STORAGE
        );

        $filePath = $this->mediaUploader->upload($media, $preStorageEvent->getRelativeFilePath(), $preStorageEvent->getFileName());
        
        return $filePath;
    }


    public function download(Media $media) {

    }

    /**
     * @param Media $media
     * @param array $mediaParameters
     * @return string
     * @throws \Exception
     */
    public function getUrl(Media $media, $mediaParameters = []) {

        if(!$this->mediaUploader->checkStorable($media)) {
            // TODO Specialize
            throw new \Exception();
        }

        // We use by default the full width image
        $urlEvent = new UrlEvent($media, $this->getRealRelativeUrl($media->getFile()), $mediaParameters);

        $this->eventDispatcher->dispatch(
            $urlEvent,
            LchMediaEvents::URL
        );

        return $urlEvent->getUrl();
    }

    /**
     * @param Media $media
     * @param array $mediaParameters
     * @return MediaTemplateEventInterface
     * @throws \Exception
     */
    public function getThumbnail(Media $media, array $mediaParameters) {
        $thumbnailEvent = new ThumbnailEvent($media);
        $thumbnailEvent->setMedia($media);
        $thumbnailEvent->setThumbnailParameters($mediaParameters);
        $thumbnailEvent->setTemplate($this->getMediaTypeConfiguration($media)[Configuration::THUMBNAIL_VIEW]);

        $this->eventDispatcher->dispatch(
            $thumbnailEvent,
            LchMediaEvents::THUMBNAIL
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
            $listItemEvent,
            LchMediaEvents::LIST_ITEM
        );

        // TODO: add one by default

        return $listItemEvent;
    }

    /**
     * @param array $authorizedMediasTypes
     * @param array $parameters
     * @param int $pageNumber
     * @return array
     */
    public function getFilteredMedias(array $authorizedMediasTypes, array $parameters, int $pageNumber) {
        $authorizedMediasQueryBuilder = $this->entityManager
            ->createQueryBuilder()
        ;

        $medias = [];

        foreach($authorizedMediasTypes as $alias => $authorizedMediasType) {

            // Initiates alias before event
            $authorizedMediasQueryBuilder
                ->addSelect($alias)
                ->from($authorizedMediasType[Configuration::ENTITY], $alias)
            ;

            // Pre search event
            $preSearchEvent = new PreSearchEvent($authorizedMediasType, $alias, $authorizedMediasQueryBuilder, $parameters);
            $this->eventDispatcher->dispatch(
                $preSearchEvent,
                LchMediaEvents::PRE_SEARCH
            );


            // Search on common media properties
            if(isset($parameters[Media::NAME])) {
                $preSearchEvent->getQueryBuilder()
                    ->orWhere($preSearchEvent->getQueryBuilder()->expr()->like("{$alias}.name", ':name'))
                    ->setParameter('name', "%{$parameters[Media::NAME]}%")
                ;
            }

            // add results limits
            $preSearchEvent->getQueryBuilder()
                ->setFirstResult(($pageNumber-1) * $authorizedMediasType[Configuration::MAX_ITEMS_PER_PAGE])
                ->setMaxResults($authorizedMediasType[Configuration::MAX_ITEMS_PER_PAGE]);

            // TODO make post event
//            // Post search event
//            $postSearchEvent = new PostSearchEvent($authorizedMediasType, $alias, $preSearchEvent->getQueryBuilder(), $parameters);
//            $this->eventDispatcher->dispatch(
//                $postSearchEvent,
//                LchMediaEvents::POST_SEARCH
//            );

            $medias = array_merge($medias, $preSearchEvent->getQueryBuilder()->getQuery()->getResult());
        }
        return $medias;
    }
    
    public function getSearchFields(string $type) {

        $searchFormEvent = new SearchFormEvent($type);
        if(isset($this->mediaTypes[$type][Configuration::SEARCH_FORM_VIEW])) {
            $searchFormEvent->setTemplate($this->mediaTypes[$type][Configuration::SEARCH_FORM_VIEW]);
        }

        $this->eventDispatcher->dispatch(
            $searchFormEvent,
            LchMediaEvents::SEARCH_FORM
        );


        return $searchFormEvent;
    }

    /**
     * @param $fullPath
     * @return string
     */
    public function getRealRelativeUrl($fullPath) {
        // CHeck path is full, with "public" inside
        if(strpos($fullPath, 'public') !== false) {
            return explode('/public', $fullPath)[1];
        }
        // If not, already relative path
        else {
            return $fullPath;
        }
    }
}