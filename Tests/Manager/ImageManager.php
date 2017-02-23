<?php

namespace Lch\MediaBundle\Tests\manager;

use Lch\MediaBundle\Manager\MediaUploader;
use Lch\MediaBundle\Model\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var  MediaUploader */
    private $imageManager;
    private $imageUplaoder;

    protected function setUp()
    {
        $this->imageUplaoder = $this->getImageUploader();
        $this->imageManager = $this->getImageManager([$this->imageUplaoder]);
    }

    public function testUpload()
    {
        $image = $this->getImage();
        $image->setName('Name');
        $image->setAlt('Alt');
        $image->setWidth(1024);
        $image->setHeight(1024);
        $image->setFile('/home/matthieu/www/lch/media/src/Lch/MediaBundle/Tests/File/Fixtures/symfony.png');

        $this->imageManager->upload($image);
    }

    /**
     * @return Image
     */
    private function getImage()
    {
        return $this->getMockBuilder('Lch\MediaBundle\Model\Image')
            ->getMockForAbstractClass();
    }

    /**
     * @param array $args
     * @return mixed
     */
    private function getImageManager(array $args)
    {
        return $this->getMockBuilder('Lch\MediaBundle\Manager\ImageManager')
            ->setConstructorArgs($args)
            ->getMockForAbstractClass();
    }

    private function getImageUploader()
    {
        return $this->getMockBuilder('Lch\MediaBundle\Uploader\ImageUploader')
            ->setConstructorArgs(['../../../web/'])
            ->getMockForAbstractClass();
    }
}