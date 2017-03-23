<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 21/03/17
 * Time: 17:22
 */

namespace Lch\MediaBundle\Listener;


use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Knp\DoctrineBehaviors\Reflection\ClassAnalyzer;
use Lch\MediaBundle\Entity\Image;
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

        if ($fileName = $entity->getFile()) {
            $entity->setFile(new File($fileName));
        }
    }
}