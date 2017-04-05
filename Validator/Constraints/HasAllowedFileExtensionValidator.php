<?php

namespace Lch\MediaBundle\Validator\Constraints;

use Lch\MediaBundle\Behavior\Storable;
use Lch\MediaBundle\DependencyInjection\Configuration;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Manager\MediaManager;
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
     * @var MediaManager
     */
    private $mediaManager;

    /**
     * MediaFileExtensionValidator constructor.
     * @param $rootDir
     * @param MediaManager $mediaManager
     */
    public function __construct($rootDir, MediaManager $mediaManager)
    {
        $this->rootDir = $rootDir;
        $this->mediaManager = $mediaManager;
    }

    /**
     * @param mixed $media
     * @param Constraint|HasAllowedFileExtension $hasAllowedFileExtensionConstraint
     * @throws \Exception
     */
    public function validate($media, Constraint $hasAllowedFileExtensionConstraint)
    {
        if ((null !== $media) && ($media instanceOf Media) && $this->mediaManager->getMediaUploader()->checkStorable($media)) {

            $allowedExtensions = $this->mediaManager->getMediaTypeConfiguration($media)[Configuration::EXTENSIONS];

            if (!in_array(strtolower($media->getFile()->guessExtension()), $allowedExtensions)) {
                $this->context->buildViolation($hasAllowedFileExtensionConstraint->getMessage())
                    ->setParameter('%extensions%', implode(', ', $allowedExtensions))
                    ->addViolation();
            }
        }
    }
}