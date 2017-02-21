<?php

namespace Lch\MediaBundle\Validator\Constraints;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ImageExtensionValidator extends ConstraintValidator
{
    private $rootDir;

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param mixed $value
     * @param Constraint|ImageExtension $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (null !== $value) {
            $allowed = false;
            $file = new File($this->rootDir.'/../web'.$value->getFile());

            if (in_array(strtolower($file->guessExtension()), $constraint->extensions)) {
                $allowed = true;
            }

            if ($allowed === false) {
                $this->context->buildViolation($constraint->message . implode(', ', $constraint->extensions))
                    ->addViolation();
            }

            return true;
        }

        return false;
    }
}