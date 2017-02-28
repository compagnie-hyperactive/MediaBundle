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
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var array
     */
    private $mediaTypes;

    /**
     * MediaExtension constructor.
     * @param MediaManager $mediaManager
     * @param \Twig_Environment $twig
     * @param array $mediaTypes
     */
    public function __construct(MediaManager $mediaManager, \Twig_Environment $twig, array $mediaTypes) {
        $this->mediaManager = $mediaManager;
        $this->twig = $twig;
        $this->mediaTypes = $mediaTypes;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getThumbnail', [$this, 'getThumbnail' ], [
                'needs_environment' => false,
                'is_safe' => ['html']
            ]),
            new \Twig_SimpleFunction('getListItem', [$this, 'getListItem' ], [
                'needs_environment' => false,
                'is_safe' => ['html']
            ])
        );
    }

    public function getThumbnail(Media $media, $width = null, $height = null)
    {
//        return $this->twig->render($this->getMediaTypeConfiguration($media)[Configuration::THUMBNAIL_VIEW],
//            ['thumbnailEvent' => $thumbnailEvent]);
        $templateEvent =  $this->mediaManager->getThumbnail($media);

        return $this->twig->render($templateEvent->getTemplate(), ['thumbnailEvent' => $templateEvent]);
//        if (null === $image) {
//            return '';
//        }
//
//        $conf = $this->getImageConf($image, $width, $height);
//
//        return "<img src='".$conf['file']."'".$conf['width']."".$conf['height']." atl='".$image->getAlt()."' />";
    }

    public function getListItem(Media $media)
    {
        $templateEvent =  $this->mediaManager->getListItem($media);

        return $this->twig->render($templateEvent->getTemplate(), ['listItemEvent' => $templateEvent]);
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
