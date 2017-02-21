<?php

namespace Lch\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

/**
 * @Annotation
 */
class ImageExtension extends Constraint
{
    public $message = 'Le fichier doit être au format ';

    /** @var array */
    public $extensions = [
        'jpg',
        'jpeg',
        'png',
        'gif',
    ];

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (isset($options['extensions'])) {
            if (is_array($options['extensions'])) {
                $this->extensions = $options['extensions'];
            } else {
                throw new InvalidOptionsException("extensions option should be an array", $options);
            }
        }
    }
}