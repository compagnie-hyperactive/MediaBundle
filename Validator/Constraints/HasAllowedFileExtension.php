<?php

namespace Lch\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

/**
 * @Annotation
 */
class HasAllowedFileExtension extends Constraint
{

    private $message = 'lch.media.extension.message';

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}