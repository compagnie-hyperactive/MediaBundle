<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 02/08/19
 * Time: 23:09
 */

namespace Lch\MediaBundle\Twig;

use Lch\MediaBundle\Manager\MediaManager;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Entity\Image;
use Symfony\Component\HttpFoundation\File\File;
use Twig\Environment;

class MediaRuntime
{
    /**
     * @var MediaManager
     */
    protected $mediaManager;

    /**
     * @var \Twig_Environment
     */
    protected $twig;
    /**
     * @var array
     */
    protected $mediaTypes;

    /**
     * MediaExtension constructor.
     * @param MediaManager $mediaManager
     * @param \Twig_Environment $twig
     * @param array $mediaTypes
     */
    public function __construct(MediaManager $mediaManager, Environment $twig, array $mediaTypes) {
        $this->mediaManager = $mediaManager;
        $this->twig = $twig;
        $this->mediaTypes = $mediaTypes;
    }

    public function getPath(Media $media, $mediaParameters = [])
    {
        /** @var File $file */
        $file = $media->getFile();
        $realPath = $file->getRealPath();
        return substr($realPath, strpos($realPath, '/public/') + 4);
    }

    /**
     * @param Media $media
     * @param array $mediaParameters
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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
     * @param Media|null $media
     * @param array $mediaParameters
     * @return string
     * @throws \Exception
     */
    public function getUrl(Media $media = null, $mediaParameters = []) {
        if($media instanceof Media) {
            return $this->mediaManager->getUrl($media, $mediaParameters);
        }
    }


    /**
     * @param string $type
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
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
     * @param Media $media
     *
     * @return mixed
     */
    public function getRealUrl(Media $media)
    {
        $fullPath = $media->getFile();

        if(strpos($fullPath, 'public') !== false) {
            return  explode('/public', $fullPath)[1];
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
}