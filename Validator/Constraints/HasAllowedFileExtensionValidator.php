<?php

namespace Lch\MediaBundle\Validator\Constraints;

use Lch\MediaBundle\DependencyInjection\Configuration;
use Lch\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HasAllowedFileExtensionValidator extends ConstraintValidator
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var array $mediaTypes
     */
    private $mediaTypes;

    /**
     * MediaFileExtensionValidator constructor.
     * @param $rootDir
     * @param array $mediaTypes
     */
    public function __construct($rootDir, array $mediaTypes)
    {
        $this->rootDir = $rootDir;
        $this->mediaTypes = $mediaTypes;
    }

    /**
     * @param mixed $media
     * @param Constraint|HasAllowedFileExtension $hasAllowedFileExtensionConstraint
     * @return bool
     * @throws \Exception
     */
    public function validate($media, Constraint $hasAllowedFileExtensionConstraint)
    {
        // TODO add check for Storable trait
        if ((null !== $media) && ($media instanceOf Media)) {
            $allowed = false;
            $file = new File($this->rootDir.'/../web'.$media->getFile());

            // TODO factorize this
            $allowedExtensions = [];
            foreach($this->mediaTypes as $mediaType) {
                if($mediaType[Configuration::ENTITY] === get_class($media)) {
                    $allowedExtensions = $mediaType[Configuration::EXTENSIONS];
                }
            }
            // TODO throw exception if no extensions
            if (in_array(strtolower($file->guessExtension()), $allowedExtensions)) {
                $allowed = true;
            }

            if ($allowed === false) {
                $this->context->buildViolation($hasAllowedFileExtensionConstraint->message . implode(', ', $allowedExtensions))
                    ->addViolation();
            }

            return true;
        }

        return false;
    }
}