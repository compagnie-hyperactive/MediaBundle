<?php

namespace Lch\MediaBundle\Loader;

use Knp\DoctrineBehaviors\Reflection\ClassAnalyzer;
use Lch\MediaBundle\Behavior\Storable;
use Lch\MediaBundle\Entity\Media;

class MediaUploader
{
    /**
     * @var ClassAnalyzer
     */
    private $classAnalyzer;

    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * @var string
     */
    private $mediaRootDir;

    /**
     * MediaUploader constructor.
     * @param ClassAnalyzer $classAnalyzer
     * @param $kernelRootDir
     * @param $mediaRootDir
     */
    public function __construct(ClassAnalyzer $classAnalyzer, $kernelRootDir, $mediaRootDir)
    {
        $this->classAnalyzer = $classAnalyzer;
        $this->kernelRootDir = $kernelRootDir;
        $this->mediaRootDir = $mediaRootDir;
    }

    /**
     * @param Media $media
     * @param $relativeFilePath
     * @param $fileName
     * @return string
     */
    public function upload(Media $media, $relativeFilePath, $fileName)
    {
        // Check storability
        $this->checkStorable($media);

        $filePath = "{$this->kernelRootDir}/../web/{$this->mediaRootDir}{$relativeFilePath}";

        if(!file_exists($filePath)) {
            mkdir($filePath, 0755, true);
        }

        try{
            $media->getFile()->move($filePath, $fileName);
        } catch (\Exception $e) {
            // TODO specialize and strenghten
            die(var_dump($e->getMessage()));
        }

        // Return relative URL
        return "/{$this->mediaRootDir}{$relativeFilePath}{$fileName}";
    }

    /**
     * Check if a media is storable (implements matching trait)
     * @param Media $media
     * @throws \Exception
     */
    public function checkStorable(Media $media) {
        // Check media is storable
        if(!$this->classAnalyzer->hasTrait(new \ReflectionClass($media), Storable::class, true)) {
            // TODO specialise
            throw new \Exception();
        }
    }
}