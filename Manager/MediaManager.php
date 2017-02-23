<?php

namespace Lch\MediaBundle\Manager;

use Doctrine\ORM\EntityManager;
use Lch\MediaBundle\DependencyInjection\Configuration;
use Lch\MediaBundle\Entity\Media;
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
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var array
     */
    private $mediaTypes;

    /**
     * MediaManager constructor.
     * @param MediaUploader $mediaUploader
     * @param EntityManager $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param \Twig_Environment $twig
     * @param array $mediaTypes
     */
    public function __construct(MediaUploader $mediaUploader, EntityManager $entityManager, EventDispatcherInterface $eventDispatcher, \Twig_Environment $twig, array $mediaTypes)
    {
        $this->mediaUploader = $mediaUploader;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->twig = $twig;
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
     * @return string
     */
    public function getThumbnail(Media $media) {
        $thumbnailEvent = new ThumbnailEvent($media);
        $this->eventDispatcher->dispatch(
            LchMediaEvents::THUMBNAIL,
            $thumbnailEvent
        );

        return $this->twig->render($this->getMediaTypeConfiguration($media)[Configuration::THUMBNAIL_VIEW],
            ['thumbnailEvent' => $thumbnailEvent]);
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