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
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use ReflectionException;
use Exception;

class DoctrineMediaListener
{
    /**
     * @var MediaUploader
     */
    private $mediaUploader;
    /**
     * @var string
     */
    private $kernelRootDir;
    /**
     * @var string
     */
    private $fallBackFilePath;

    /**
     * DoctrineImageListener constructor.
     * @param MediaUploader $mediaUploader
     * @param $kernelRootDir
     * @param $fallBackFilePath
     */
    public function __construct(MediaUploader $mediaUploader, $kernelRootDir, $fallBackFilePath) {
        $this->mediaUploader = $mediaUploader;
        $this->kernelRootDir = $kernelRootDir;
        $this->fallBackFilePath = $fallBackFilePath;
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


    /**
     * @param LifecycleEventArgs $args
     *
     * @throws ReflectionException
     * @throws FileNotFoundException
     * @throws Exception
     */
    private function handleFile(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if (!$entity instanceof Media || !$this->mediaUploader->checkStorable($entity)) {
            return;
        }

        if (!$entity->getFile() instanceof File && $fileName = $this->getRealRelativeUrl($entity->getFile())) {
            // TODO make this configurable
            $publicDirectory = "{$this->kernelRootDir}/public";
            try {
                $entity->setFile(new File("{$publicDirectory}{$fileName}"));
            } catch (Exception $exception) {
                switch (get_class($exception)) {
                    case FileNotFoundException::class: {
                        // rethrow exception
                        if (empty($this->fallBackFilePath)) throw $exception;
                        if (!file_exists($this->fallBackFilePath)) {
                            $this->fallBackFilePath = "{$publicDirectory}".$this->fallBackFilePath;
                            if (!file_exists($this->fallBackFilePath)) {
                                throw $exception;
                            }
                        }
                        $entity->setFile(new File($this->fallBackFilePath));
                        $entity->setFallBack(true);
                        break;
                    }
                    default: {
                        // unknown exception so rethrow it
                        throw $exception;
                        break;
                    }
                }
            }
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