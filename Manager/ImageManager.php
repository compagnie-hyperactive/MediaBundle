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
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

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
     * @throws \Exception
     */
    public function generateThumbnails(Image $image) {
        // Get thumbnails info
        $imageTypeInfo = $this->mediaManager->getMediaTypeConfiguration($image);

        // Only if thumbnails wanted
        if(isset($imageTypeInfo[Configuration::THUMBNAIL_SIZES])) {

            foreach($imageTypeInfo[Configuration::THUMBNAIL_SIZES] as $thumbnailSlug => $size) {
//                $imagePath = "{$this->mediaManager->getMediaUploader()->getWebRootDir()}{$definitivePath}";
                $imagePath = $image->getFile()->getRealPath();

                //Resizing strategy
                if (empty($size[Configuration::STRATEGY])){
                    throw new FileNotFoundException('Resize image strategy is not defined for size "'.$thumbnailSlug.'"');
                }
                
                $strategy = $size[Configuration::STRATEGY];

                if(!file_exists($imagePath)) {
                    throw new FileNotFoundException('Image doesn\'t exist');
                }
                // Imagick case
                if (extension_loaded('imagick')) {
                    // load image
                    $i = new \Imagick($imagePath);

                    // Get size
                    $imageSize = $i->getImageGeometry();

                    // Setup crop start point
                    if ($strategy == Configuration::CROP_STRATEGY) {
                        $cropStartPoint = ["x" => 0, "y" => 0];
                        $i->cropImage($size[Configuration::WIDTH], $size[Configuration::HEIGHT], $cropStartPoint['x'], $cropStartPoint['y']);
                    }
                    else if ($strategy == Configuration::RESIZE_STRATEGY){
                        /**
                         * TODO: treat different resize cases
                         *
                         *     Demand    Picture
                         * 1:  W > H   |  W > H
                         * 2:  W > H   |  W < H
                         * 3:  W > H   |  W = H
                         * 4:  W > H   | (W or H) < Demand
                         * 4': W > H   | (W and H) < Demand

                         * 5:  W < H   |  W < H
                         * 6:  W < H   |  W > H
                         * 7:  W < H   |  W = H
                         * 8:  W < H   | (W or H) < Demand
                         * 8': W < H   | (W and H) < Demand
                         *
                         * 9:  W = H   |  W != H
                         * 10: W = H   |  W = H
                         *
                         */

                        //Resize picture keep aspect ratio, upscale allowed.
                        if ($imageSize['width'] > $imageSize['height']){
                            $i->scaleImage($size[Configuration::WIDTH],0);
                        }
                        else {
                            $i->scaleImage(0, $size[Configuration::HEIGHT]);
                        }
                    }

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