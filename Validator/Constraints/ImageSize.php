<?php

namespace Lch\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ImageSize extends Constraint
{
    protected $message = 'erreur format image';

    protected $widthMessage = 'lch.media.image.width.message';
    protected $heightMessage = 'lch.media.image.height.message';
    protected $minWidthMessage = 'lch.media.image.width.min.message';
    protected $maxWidthMessage = 'lch.media.image.width.max.message';
    protected $minHeightMessage = 'lch.media.image.height.min.message';
    protected $maxHeightMessage = 'lch.media.image.height.max.message';

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getWidthMessage(): string
    {
        return $this->widthMessage;
    }

    /**
     * @return string
     */
    public function getHeightMessage(): string
    {
        return $this->heightMessage;
    }

    /**
     * @return string
     */
    public function getMinWidthMessage(): string
    {
        return $this->minWidthMessage;
    }

    /**
     * @return string
     */
    public function getMaxWidthMessage(): string
    {
        return $this->maxWidthMessage;
    }

    /**
     * @return string
     */
    public function getMinHeightMessage(): string
    {
        return $this->minHeightMessage;
    }

    /**
     * @return string
     */
    public function getMaxHeightMessage(): string
    {
        return $this->maxHeightMessage;
    }

    /**
     * @inheritdoc
     */
    public function getTargets() {
        return [
            self::CLASS_CONSTRAINT,
            self::PROPERTY_CONSTRAINT
        ];
    }

}