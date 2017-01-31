<?php

namespace Lch\MediaBundle\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    private $rootDir;

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function upload($file)
    {
        if (is_string($file)) {
            $fileName = explode('\\',$file);
            $file = new UploadedFile($file, end($fileName));
        }
        $rootPath = "/uploads/images/" . date('Y') . "/" . date('m') . "/";

        $imagesDir = "{$this->rootDir}/../web{$rootPath}";

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