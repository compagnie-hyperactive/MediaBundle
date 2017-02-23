<?php

namespace Lch\MediaBundle\Manager;

use Doctrine\ORM\EntityManager;
use Lch\MediaBundle\DependencyInjection\Configuration;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Model\ImageInterface;
use Lch\MediaBundle\Uploader\MediaUploader;

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
     * MediaManager constructor.
     * @param MediaUploader $mediaUploader
     * @param EntityManager $entityManager
     */
    public function __construct(MediaUploader $mediaUploader, EntityManager $entityManager)
    {
        $this->mediaUploader = $mediaUploader;
        $this->entityManager = $entityManager;
    }

    // TODO review
    public function upload(Media $media)
    {
        // TODO add check on storage trait. Throw exception if not
        
        $fileName = $this->mediaUploader->upload($media->getFilePath());

        return $fileName;
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

}