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

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getThumbnail', [$this, 'getThumbnail' ], [
                'needs_environment' => false,
                'is_safe' => ['html']
            ]),
            new \Twig_SimpleFunction('getThumbnailUrl', [$this, 'getThumbnailUrl' ], [
                'needs_environment' => false,
            ]),
            new \Twig_SimpleFunction('getListItem', [$this, 'getListItem' ], [
                'needs_environment' => false,
                'is_safe' => ['html']
            ]),
            new \Twig_SimpleFunction('getUrl', [$this, 'getUrl' ], [
                'needs_environment' => false
            ]),
            new \Twig_SimpleFunction('getSearchFields', [$this, 'getSearchFields' ], [
                'needs_environment' => false,
                'is_safe' => ['html']
            ]),
        );
    }

    /**
     * @param Media $media
    
     * @return string
     */
    public function getThumbnail(Media $media, $mediaParameters = [])
    {
        $templateEvent =  $this->mediaManager->getThumbnail($media, $mediaParameters);
        return $this->twig->render($templateEvent->getTemplate(), ['thumbnailEvent' => $templateEvent]);
    }


    /**
     * @param Media $media

     * @return string
     */
    public function getThumbnailUrl(Media $media, $mediaParameters = [])
    {
        $templateEvent =  $this->mediaManager->getThumbnail($media, $mediaParameters);
        return $templateEvent->getThumbnailPath();
    }

    /**
     * @param Media $media
     * @param array $attributes
     * @return string
     */
    public function getListItem(Media $media, array $attributes = [])
    {
        // TODO modifications here should be duplicate on MediaController::addAction
        $templateEvent =  $this->mediaManager->getListItem($media);

        return $this->twig->render($templateEvent->getTemplate(), [
                'listItemEvent' => $templateEvent,
                'attributes' => $attributes
            ]
        );
    }

    /**
     * @param Media $media
     * @param array $mediaParameters
     * @return string
     * @throws \Exception
     */
    public function getUrl(Media $media, $mediaParameters = []) {
        return $this->mediaManager->getUrl($media, $mediaParameters);
    }


    public function getSearchFields(string $type) {
        $searchFormEvent =  $this->mediaManager->getSearchFields($type);

        if(null != $searchFormEvent->getTemplate()) {
            return $this->twig->render($searchFormEvent->getTemplate(), [
                    'searchFormEvent' => $searchFormEvent
                ]
            );
        }
        else {
            return '';
        }

    }

    /**
     * @param Image $image
     * @param null $width
     * @param null $height
     * @return array
     */
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
