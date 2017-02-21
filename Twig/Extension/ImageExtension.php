<?php

namespace Lch\MediaBundle\Twig\Extension;

use Lch\MediaBundle\Model\ImageInterface;

class ImageExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('image', [$this, 'image' ], [
                'needs_environment' => false,
                'is_safe' => ['html']
            ])
        );
    }

    public function image(ImageInterface $image = null, $width = null, $height = null)
    {
        if (null === $image) {
            return '';
        }

        $conf = $this->getImageConf($image, $width, $height);

        return "<img src='".$conf['file']."'".$conf['width']."".$conf['height']." atl='".$image->getAlt()."' />";
    }

    protected function getImageConf(ImageInterface $image, $width = null, $height = null)
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
