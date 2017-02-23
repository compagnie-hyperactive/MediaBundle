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
     * @var array $mediaTypes
     */
    private $mediaTypes;

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
     * @return bool
     * @throws \Exception
     */
    public function validate($media, Constraint $hasAllowedFileExtensionConstraint)
    {
        if ((null !== $media) && ($media instanceOf Media) && in_array(Storable::class, class_uses($media))) {
            $allowed = false;
            $file = new File($this->rootDir.'/../web'.$media->getFile());


//            $allowedExtensions = [];
//            foreach($this->mediaTypes as $mediaType) {
//                // TODO find wwwwayyy better (due to Proxy class)
//                if(strpos(get_class($media), $mediaType[Configuration::ENTITY]) !== false) {
//                    $allowedExtensions = $mediaType[Configuration::EXTENSIONS];
//                }
//            }
            $allowedExtensions = $this->mediaManager->getMediaTypeConfiguration($media)[Configuration::EXTENSIONS];

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