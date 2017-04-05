<?php

namespace Lch\MediaBundle\Validator\Constraints;

use Lch\MediaBundle\Entity\Image;
use Lch\MediaBundle\Form\AddOrChooseMediaType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

class ImageSizeValidator extends ConstraintValidator
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ImageSizeValidator constructor.
     * @param Session $session
     * @param TranslatorInterface $translator
     */
    public function __construct(Session $session, TranslatorInterface $translator) {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @param mixed $image
     * @param Constraint $constraint
     */
    public function validate($image, Constraint $constraint)
    {

        if (null !== $image) {

            if (!$image instanceof Image) {
                throw new ValidatorException('ImageSize constraint should be used on an Image object');
            }

            if(!$constraint instanceof ImageSize) {
                throw new ValidatorException('Constraint should be used on an ImageSize constraint type');
            }

            // Only if property name specified (I.E. in ofrm child with properties set)
            if(null !== $this->context->getPropertyName()) {
                $targetImageType = $this->context->getRoot()->get($this->context->getPropertyName());
                $targetImageTypeOptions = $targetImageType->getConfig()->getOptions();

                // Exact width
                if ($targetImageTypeOptions[AddOrChooseMediaType::IMAGE_WIDTH] && $targetImageTypeOptions[AddOrChooseMediaType::IMAGE_WIDTH] !== $image->getWidth()) {
                    $this->context
                        ->buildViolation($constraint->getWidthMessage())
                        ->setParameter('%target_width%', $targetImageTypeOptions[AddOrChooseMediaType::IMAGE_WIDTH])
                        ->setParameter('%given_width%', $image->getWidth())
                        ->setParameter('%field%', $this->context->getPropertyName())
                        ->atPath($this->context->getPropertyName())
                        ->addViolation();

                    $this->session->getFlashBag()->add('danger',
                        $this->translator->trans(
                            $constraint->getWidthMessage(), [
                            '%target_width%' => $targetImageTypeOptions[AddOrChooseMediaType::IMAGE_WIDTH],
                            '%given_width%' => $image->getWidth(),
                            '%field%' => $this->context->getPropertyName()
                        ], 'validators'
                        )
                    );
                }

                // Exact height
                if ($targetImageTypeOptions[AddOrChooseMediaType::IMAGE_HEIGHT] && $targetImageTypeOptions[AddOrChooseMediaType::IMAGE_HEIGHT] !== $image->getHeight()) {
                    $this->context
                        ->buildViolation($constraint->getHeightMessage())
                        ->setParameter('%target_height%', $targetImageTypeOptions[AddOrChooseMediaType::IMAGE_HEIGHT])
                        ->setParameter('%given_height%', $image->getHeight())
                        ->setParameter('%field%', $this->context->getPropertyName())
                        ->atPath($this->context->getPropertyName())
                        ->addViolation();

                    $this->session->getFlashBag()->add('danger',
                        $this->translator->trans(
                            $constraint->getHeightMessage(), [
                            '%target_height%' => $targetImageTypeOptions[AddOrChooseMediaType::IMAGE_HEIGHT],
                            '%given_height%' => $image->getHeight(),
                            '%field%' => $this->context->getPropertyName()
                        ], 'validators'
                        )
                    );
                }

                // Min width
                if ($targetImageTypeOptions[AddOrChooseMediaType::MIN_IMAGE_WIDTH] && $targetImageTypeOptions[AddOrChooseMediaType::MIN_IMAGE_WIDTH] > $image->getWidth()) {
                    $this->context
                        ->buildViolation($constraint->getMinWidthMessage())
                        ->setParameter('%target_width%', $targetImageTypeOptions[AddOrChooseMediaType::MIN_IMAGE_WIDTH])
                        ->setParameter('%given_width%', $image->getWidth())
                        ->setParameter('%field%', $this->context->getPropertyName())
                        ->atPath($this->context->getPropertyName())
                        ->addViolation();

                    $this->session->getFlashBag()->add('danger',
                        $this->translator->trans(
                            $constraint->getMinWidthMessage(), [
                            '%target_width%' => $targetImageTypeOptions[AddOrChooseMediaType::MIN_IMAGE_WIDTH],
                            '%given_width%' => $image->getWidth(),
                            '%field%' => $this->context->getPropertyName()
                        ], 'validators'
                        )
                    );
                }

                // Min height
                if ($targetImageTypeOptions[AddOrChooseMediaType::MIN_IMAGE_HEIGHT] && $targetImageTypeOptions[AddOrChooseMediaType::MIN_IMAGE_HEIGHT] > $image->getHeight()) {
                    $this->context
                        ->buildViolation($constraint->getMinHeightMessage())
                        ->setParameter('%target_height%', $targetImageTypeOptions[AddOrChooseMediaType::MIN_IMAGE_HEIGHT])
                        ->setParameter('%given_height%', $image->getHeight())
                        ->setParameter('%field%', $this->context->getPropertyName())
                        ->atPath($this->context->getPropertyName())
                        ->addViolation();

                    $this->session->getFlashBag()->add('danger',
                        $this->translator->trans(
                            $constraint->getMinHeightMessage(), [
                            '%target_height%' => $targetImageTypeOptions[AddOrChooseMediaType::MIN_IMAGE_HEIGHT],
                            '%given_height%' => $image->getHeight(),
                            '%field%' => $this->context->getPropertyName()
                        ], 'validators'
                        )
                    );
                }

                // Max width
                if ($targetImageTypeOptions[AddOrChooseMediaType::MAX_IMAGE_WIDTH] && $targetImageTypeOptions[AddOrChooseMediaType::MAX_IMAGE_WIDTH] < $image->getWidth()) {
                    $this->context
                        ->buildViolation($constraint->getMaxWidthMessage())
                        ->setParameter('%target_width%', $targetImageTypeOptions[AddOrChooseMediaType::MAX_IMAGE_WIDTH])
                        ->setParameter('%given_width%', $image->getWidth())
                        ->setParameter('%field%', $this->context->getPropertyName())
                        ->atPath($this->context->getPropertyName())
                        ->addViolation();

                    $this->session->getFlashBag()->add('danger',
                        $this->translator->trans(
                            $constraint->getMaxWidthMessage(), [
                            '%target_width%' => $targetImageTypeOptions[AddOrChooseMediaType::MAX_IMAGE_WIDTH],
                            '%given_width%' => $image->getWidth(),
                            '%field%' => $this->context->getPropertyName()
                        ], 'validators'
                        )
                    );
                }

                // Max height
                if ($targetImageTypeOptions[AddOrChooseMediaType::MAX_IMAGE_HEIGHT] && $targetImageTypeOptions[AddOrChooseMediaType::MAX_IMAGE_HEIGHT] < $image->getHeight()) {
                    $this->context
                        ->buildViolation($constraint->getMaxHeightMessage())
                        ->setParameter('%target_height%', $targetImageTypeOptions[AddOrChooseMediaType::MAX_IMAGE_HEIGHT])
                        ->setParameter('%given_height%', $image->getHeight())
                        ->setParameter('%field%', $this->context->getPropertyName())
                        ->atPath($this->context->getPropertyName())
                        ->addViolation();

                    $this->session->getFlashBag()->add('danger',
                        $this->translator->trans(
                            $constraint->getMaxHeightMessage(), [
                            '%target_height%' => $targetImageTypeOptions[AddOrChooseMediaType::MAX_IMAGE_HEIGHT],
                            '%given_height%' => $image->getHeight(),
                            '%field%' => $this->context->getPropertyName()
                        ], 'validators'
                        )
                    );
                }
            }
        }
    }
}