<?php

namespace Lch\MediaBundle\Manager;

use Lch\MediaBundle\Model\ImageInterface;
use Lch\MediaBundle\Uploader\ImageUploader;

class ImageManager
{
    private $uploader;

    public function __construct(ImageUploader $imageUploader)
    {
        $this->uploader = $imageUploader;
    }

    public function upload(ImageInterface $image)
    {
        $imageSite = getimagesize($image->getFile());

        $image->setWidth($imageSite[0]);
        $image->setHeight($imageSite[1]);

        $fileName = $this->uploader->upload($image->getFile());

        return $fileName;
    }

}