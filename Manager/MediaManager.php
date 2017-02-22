<?php

namespace Lch\MediaBundle\Manager;

use Lch\MediaBundle\Model\ImageInterface;
use Lch\MediaBundle\Uploader\MediaUploader;

class MediaManager
{
    private $mediaUploader;

    public function __construct(MediaUploader $mediaUploader)
    {
        $this->mediaUploader = $mediaUploader;
    }

    // TODO review
    public function upload(ImageInterface $image)
    {
        $imageSite = getimagesize($image->getFile());

        $image->setWidth($imageSite[0]);
        $image->setHeight($imageSite[1]);
x
        $fileName = $this->mediaUploader->upload($image->getFile());

        return $fileName;
    }

    /**
     * @param array $authorizedTypes
     * @return array
     */
    public function filter(array $authorizedTypes) {
        return [];
    }

}