<?php

namespace Lch\MediaBundle\Validator\Constraints;

use Lch\MediaBundle\Behavior\Storable;
use Lch\MediaBundle\DependencyInjection\Configuration;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Form\AddOrChooseMediaType;
use Lch\MediaBundle\Manager\MediaManager;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

class MediaWeightValidator extends ConstraintValidator
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
     * @param Constraint|MediaWeightValidator $weightConstraint
     * @throws \Exception
     */
    public function validate($media, Constraint $weightConstraint)
    {
        if ((null !== $media) && ($media instanceOf Media) && $this->mediaManager->getMediaUploader()->checkStorable($media)) {


            if(!$weightConstraint instanceof MediaWeight) {
                throw new ValidatorException('Constraint should be used on an MediaWeight constraint type');
            }

            // Only if property name specified (I.E. in form child with properties set)
            if(null !== $this->context->getPropertyName()) {
                $targetImageType = $this->context->getRoot()->get($this->context->getPropertyName());
                $targetImageTypeOptions = $targetImageType->getConfig()->getOptions();

                // Min weight
                if ($targetImageTypeOptions[AddOrChooseMediaType::MIN_MEDIA_WEIGHT] && $targetImageTypeOptions[AddOrChooseMediaType::MIN_MEDIA_WEIGHT] > ($media->getFile()->getSize() / 1000)) {
                    $this->context
                        ->buildViolation($weightConstraint->getMinWeightMessage())
                        ->setParameter('%target_weight%', $targetImageTypeOptions[AddOrChooseMediaType::MIN_MEDIA_WEIGHT])
                        ->setParameter('%given_weight%', ($media->getFile()->getSize() / 1000))
                        ->setParameter('%field%', $this->context->getPropertyName())
                        ->atPath($this->context->getPropertyName())
                        ->addViolation();
                }

                // Max weight
                if ($targetImageTypeOptions[AddOrChooseMediaType::MAX_MEDIA_WEIGHT] && $targetImageTypeOptions[AddOrChooseMediaType::MAX_MEDIA_WEIGHT] < ($media->getFile()->getSize() / 1000)) {
                    $this->context
                        ->buildViolation($weightConstraint->getMaxWeightMessage())
                        ->setParameter('%target_weight%', $targetImageTypeOptions[AddOrChooseMediaType::MAX_MEDIA_WEIGHT])
                        ->setParameter('%given_weight%', ($media->getFile()->getSize() / 1000))
                        ->setParameter('%field%', $this->context->getPropertyName())
                        ->atPath($this->context->getPropertyName())
                        ->addViolation();
                }
            }
        }
    }
}