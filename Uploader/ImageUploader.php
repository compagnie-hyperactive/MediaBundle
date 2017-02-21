<?php

namespace Lch\MediaBundle\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * @var string
     */
    private $mediaRootDir;

    public function __construct($kernelRootDir, $mediaRootDir)
    {
        $this->kernelRootDir = $kernelRootDir;
        $this->mediaRootDir = $mediaRootDir;
    }

    public function upload($file)
    {
        if (is_string($file)) {
            $fileName = explode('\\',$file);
            $file = new UploadedFile($file, end($fileName));
        }
        $rootPath = "/{$this->mediaRootDir}/" . date('Y') . "/" . date('m') . "/";

        $imagesDir = "{$this->kernelRootDir}/../web{$rootPath}";

        if(!file_exists($imagesDir)) {
            mkdir($imagesDir, 0755, true);
        }

        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        try{
            $file->move($imagesDir, $fileName);
        } catch (\Exception $e) {
            die(var_dump($e->getMessage()));
        }

        return $rootPath.$fileName;
    }
}