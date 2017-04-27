<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 25/04/17
 * Time: 15:08
 */

namespace Lch\MediaBundle\Manager;


use Lch\MediaBundle\Entity\Image;
use Lch\MediaBundle\DependencyInjection\Configuration;

class ImageManager
{
    /**
     * @var MediaManager
     */
    private $mediaManager;

    public function __construct(MediaManager $mediaManager) {
        $this->mediaManager = $mediaManager;
    }

    /**
     * @return MediaManager
     */
    public function getMediaManager()
    {
        return $this->mediaManager;
    }

    /**
     * @param Image $image
     * @param $definitivePath
     * @throws \Exception
     */
    public function generateThumbnails(Image $image, $definitivePath) {
        // Get thumbnails info
        $imageTypeInfo = $this->mediaManager->getMediaTypeConfiguration($image);

        // Only if thumbnails wanted
        if(isset($imageTypeInfo[Configuration::THUMBNAIL_SIZES])) {
            foreach($imageTypeInfo[Configuration::THUMBNAIL_SIZES] as $thumbnailSlug => $size) {
                $imagePath = "{$this->mediaManager->getMediaUploader()->getWebRootDir()}{$definitivePath}";

                // Imagick case
                if (extension_loaded('imagick')) {
                    // load image
                    $i = new \Imagick($imagePath);

                    // Get size
                    $imageSize = $i->getImageGeometry();

                    // Setup crop start point
                    $cropStartPoint = ["x" => 0, "y" => 0];
                    $i->cropImage($size[Configuration::WIDTH], $size[Configuration::HEIGHT], $cropStartPoint['x'], $cropStartPoint['y']);

                    $imagePathinfo = pathinfo($imagePath);

//                    $i->writeImage("{$imagePathinfo['dirname']}/{$imagePathinfo['filename']}_{$size[Configuration::WIDTH]}_{$size[Configuration::HEIGHT]}.{$imagePathinfo['extension']}");
                    $i->writeImage($this->thumbnailNamingStrategy($imagePathinfo, $thumbnailSlug, $size));
                }
                // TODO GD
                else if (extension_loaded('gd')) {

                }
                else {
                    // TODO add log, alert user
                }
            }
        }
    }

    /**
     * @param Image $image
     * @param string $size
     * @return string
     * @throws \Exception
     */
    public function getThumbnailUrl(Image $image, string $size) {
        $mediaTypeData = $this->getMediaManager()->getMediaTypeConfiguration($image);

        // Check size asked is setup for the media
        if(isset($mediaTypeData[Configuration::THUMBNAIL_SIZES][$size])) {
            $thumbnailPath = $this->thumbnailNamingStrategy(pathinfo($image->getFile()->getRealPath()), $size, $mediaTypeData[Configuration::THUMBNAIL_SIZES][$size]);

            if(file_exists($thumbnailPath)) {
                return $this->getMediaManager()->getRealRelativeUrl($thumbnailPath);
            }
        } else {
            // TODO add log that size is not defined
        }
        return "";
    }

    /**
     * Strategy to name the thumbnail file name
     * @param array $imagePathinfo
     * @param string $thumbnailSlug
     * @param array $size
     * @return string the thumbnail name
     */
    private function thumbnailNamingStrategy(array $imagePathinfo, string $thumbnailSlug, array $size) {
        return "{$imagePathinfo['dirname']}/{$imagePathinfo['filename']}_{$thumbnailSlug}.{$imagePathinfo['extension']}";
    }
}