<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 16/03/17
 * Time: 13:28
 */

namespace Lch\MediaBundle\Loader;


use Lch\MediaBundle\Entity\Media;

class MediaDownloader
{
    /**
     * @var MediaManager
     */
    private $mediaManager;

    /**
     * MediaDownloader constructor.
     * @param MediaManager $mediaManager
     */
    public function __construct(MediaManager $mediaManager) {
        $this->mediaManager = $mediaManager;
    }



    public function download(Media $media) {

    }
}