<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 21/03/17
 * Time: 17:22
 */

namespace Lch\MediaBundle\Listener;


use Doctrine\Persistence\Event\LifecycleEventArgs;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Loader\MediaUploader;
use Symfony\Component\HttpFoundation\File\File;

class DoctrineMediaListener
{
    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * @var MediaUploader
     */
    private $mediaUploader;

    /**
     * DoctrineImageListener constructor.
     * @param $kernelRootDir
     * @param MediaUploader $mediaUploader
     */
    public function __construct(MediaUploader $mediaUploader, $kernelRootDir) {
        $this->mediaUploader = $mediaUploader;
        $this->kernelRootDir = $kernelRootDir;
    }


    /**
     * Once persisted, reload File
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args) {
        $this->handleFile($args);
    }

    public function postLoad(LifecycleEventArgs $args) {
        $this->handleFile($args);
    }


    private function handleFile(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if (!$entity instanceof Media || !$this->mediaUploader->checkStorable($entity)) {
            return;
        }

        if (!$entity->getFile() instanceof File && $fileName = $this->getRealRelativeUrl($entity->getFile())) {
            // TODO make this configurable
            $entity->setFile(new File("{$this->kernelRootDir}/public{$fileName}"));
        }
    }


    /**
     * @param $fullPath
     * @return mixed
     */
    private function getRealRelativeUrl($fullPath) {
        // CHeck path is full, with "web" inside
        if(strpos($fullPath, 'public') !== false) {
            return explode('/public', $fullPath)[1];
        }
        // If not, already relative path
        else {
            return $fullPath;
        }
    }
}