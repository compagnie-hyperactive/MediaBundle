<?php

namespace Lch\MediaBundle\Twig\Extension;

use Lch\MediaBundle\Entity\Image;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Manager\MediaManager;

class MediaExtension extends \Twig_Extension
{
    /**
     * @var MediaManager
     */
    private $mediaManager;

    /**
     * @var array
     */
    private $mediaTypes;

    /**
     * MediaExtension constructor.
     * @param MediaManager $mediaManager
     * @param array $mediaTypes
     */
    public function __construct(MediaManager $mediaManager, array $mediaTypes) {
        $this->mediaTypes = $mediaTypes;
        $this->mediaManager = $mediaManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getThumbnail', [$this, 'getThumbnail' ], [
                'needs_environment' => false,
                'is_safe' => ['html']
            ])
        );
    }

    public function getThumbnail(Media $image, $width = null, $height = null)
    {
        return $this->mediaManager->getThumbnail($image);
//        if (null === $image) {
//            return '';
//        }
//
//        $conf = $this->getImageConf($image, $width, $height);
//
//        return "<img src='".$conf['file']."'".$conf['width']."".$conf['height']." atl='".$image->getAlt()."' />";
    }

    protected function getImageConf(Image $image, $width = null, $height = null)
    {
        $renderWidth = '';
        if (null !== $width) {
            $renderWidth = ' width="'.$width.'" ';
        }

        $renderHeight = '';
        if (null !== $height) {
            $renderHeight = ' height="'.$height.'"';
        }

        return [
            'file' => $image->getFile(),
            'alt' => $image->getAlt(),
            'width' => $renderWidth,
            'height' => $renderHeight,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lch.media_bundle.image';
    }
}
