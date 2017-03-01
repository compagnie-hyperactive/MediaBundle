<?php

namespace Lch\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

/**
 * @Annotation
 */
class ImageFormat extends Constraint
{
    public $message = 'erreur format image';

    public $minWidth;
    public $minWidthMessage = 'lch.media_bundle.image.width.min_message';
    public $maxWidth;
    public $maxWidthMessage = 'lch.media_bundle.image.width.max_message';

    public $minHeight;
    public $minHeightMessage = 'lch.media_bundle.image.height.min_message';
    public $maxHeight;
    public $maxHeightMessage = 'lch.media_bundle.image.height.max_message';

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (isset($options['minWidth'])) {
            if (is_int($options['minWidth'])) {
                $this->minWidth = $options['minWidth'];
            } else {
                throw new InvalidOptionsException("minWidth option should be an Integer", $options);
            }
        }

        if (isset($options['maxWidth'])) {
            if (is_int($options['maxWidth'])) {
                $this->maxWidth = $options['maxWidth'];
            } else {
                throw new InvalidOptionsException("maxWidth option should be an Integer", $options);
            }
        }

        if (isset($options['minHeight'])) {
            if (is_int($options['minHeight'])) {
                $this->minHeight = $options['minHeight'];
            } else {
                throw new InvalidOptionsException("minHeight option should be an Integer", $options);
            }
        }

        if (isset($options['maxHeight'])) {
            if (is_int($options['maxHeight'])) {
                $this->maxHeight = $options['maxHeight'];
            } else {
                throw new InvalidOptionsException("maxHeight option should be an Integer", $options);
            }
        }
    }

    public function getMinWidth()
    {
        return $this->minWidth;
    }

    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    public function getMinHeight()
    {
        return $this->minHeight;
    }

    public function getMaxHeight()
    {
        return $this->maxHeight;
    }
}