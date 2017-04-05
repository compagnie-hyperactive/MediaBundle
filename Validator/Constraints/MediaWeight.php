<?php

namespace Lch\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

/**
 * @Annotation
 */
class MediaWeight extends Constraint
{
    private $minWeightMessage = 'lch.media.weight.min.message';
    private $maxWeightMessage = 'lch.media.weight.max.message';

    /**
     * @return string
     */
    public function getMinWeightMessage(): string
    {
        return $this->minWeightMessage;
    }

    /**
     * @return string
     */
    public function getMaxWeightMessage(): string
    {
        return $this->maxWeightMessage;
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