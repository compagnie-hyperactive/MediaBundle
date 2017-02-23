<?php

namespace Lch\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

/**
 * @Annotation
 */
class HasAllowedFileExtension extends Constraint
{
    public $message = 'Le fichier doit être au format ';
}