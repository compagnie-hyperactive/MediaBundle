<?php
/**
 * Created by PhpStorm.
 * User: Benoit
 * Date: 11/05/2017
 * Time: 11:23
 */

namespace Lch\MediaBundle\Manager;


use Lch\MediaBundle\Entity\Media;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class PdfManager
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
     * Generate thumbnail for PDF type media
     * @param Media $media
     * @throws \Exception
     */
    public function generateThumbnail(Media $media) {

        if(!$media instanceof Media &&
            !$media->getFile()->getExtension() == 'pdf' &&
            !$media->getFile()->getExtension() == 'PDF') {

            return;
        }

        // Get thumbnails info
        //$mediaTypeInfo = $this->getMediaTypeConfiguration($media);

        $mediaPath = $media->getFile()->getRealPath();
        if(!file_exists($mediaPath)) {
            throw new FileNotFoundException('Media doesn\'t exist');
        }
        // Imagick case
        if (extension_loaded('imagick')) {

            $sizes = [
                "thumbnail" => 75,
                "preview" => 500
            ];

            foreach($sizes as $slug => $max) {
                // load pdf
                $i = new \Imagick($mediaPath . "[0]");
                //set new format
                $i->setImageFormat('png');
                $mediaPathInfo = pathinfo($mediaPath);

                // Get size
                $imageSize = $i->getImageGeometry();

                // Resize picture keep aspect ratio, upscale allowed.
                if ($imageSize['width'] > $imageSize['height']){
                    $i->scaleImage($max,0);
                }
                else {
                    $i->scaleImage(0, $max);
                }
                $fileName = $this->thumbnailNamingStrategy($mediaPathInfo, $slug, []);
                $i->writeImages($fileName,false);
            }
        }
    }

    /**
     * @param Media $media
     * @param string $size
     * @return string
     * @throws \Exception
     */
    public function getThumbnailUrl(Media $media, string $size = 'thumbnail') {

        $thumbnailPath = $this->thumbnailNamingStrategy(pathinfo($media->getFile()->getRealPath()), $size);

        if(file_exists($thumbnailPath)) {
            return $this->getMediaManager()->getRealRelativeUrl($thumbnailPath);
        }

        return "";
    }

    /**
     * Strategy to name the thumbnail file name
     * @param array $mediaPathinfo
     * @param string $thumbnailSlug
     * @param array $size
     * @return string the thumbnail name
     */
    private function thumbnailNamingStrategy(array $mediaPathinfo, string $thumbnailSlug, array $size = []) {
        return "{$mediaPathinfo['dirname']}/{$mediaPathinfo['filename']}_{$thumbnailSlug}.png";
    }
}