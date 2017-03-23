<?php

namespace Lch\MediaBundle\Validator\Constraints;

use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Model\ImageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

class ImageSizeValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @return bool
     */
    public function validate($value, Constraint $constraint)
    {
        if (null !== $value) {

            if (!$value instanceof Media) {
                throw new ValidatorException('ImageFormat Constraint should be used on an Media object');
            }

//            if (null !== $constraint->getMinWidth() && $value->getWidth() < $constraint->getMinWidth()) {
//                $this->context
//                    ->buildViolation($constraint->minWidthMessage)
//                    ->setParameter('%minWidth%', $constraint->getMinWidth())
//                    ->setParameter('%field%', $this->context->getPropertyName())
//                    ->atPath($this->context->getPropertyName())
//                    ->addViolation();
//            }
//
//            if (null !== $constraint->getMaxWidth() && $value->getWidth() > $constraint->getMaxWidth()) {
//                $this->context
//                    ->buildViolation($constraint->maxWidthMessage)
//                    ->setParameter('%maxWidth%', $constraint->getMaxWidth())
//                    ->setParameter('%field%', $this->context->getPropertyName())
//                    ->atPath($this->context->getPropertyName())
//                    ->addViolation();
//            }
//
//            if (null !== $constraint->getMinHeight() && $value->getHeight() < $constraint->getMinHeight()) {
//                $this->context
//                    ->buildViolation($constraint->minHeightMessage)
//                    ->setParameter('%minHeight%', $constraint->getMinHeight())
//                    ->setParameter('%field%', $this->context->getPropertyName())
//                    ->atPath($this->context->getPropertyName())
//                    ->addViolation();
//            }
//
//            if (null !== $constraint->getMaxHeight() && $value->getHeight() > $constraint->getMaxHeight()) {
//                $this->context
//                    ->buildViolation($constraint->maxHeightMessage)
//                    ->setParameter('%maxHeight%', $constraint->getMaxHeight())
//                    ->setParameter('%field%', $this->context->getPropertyName())
//                    ->atPath($this->context->getPropertyName())
//                    ->addViolation();
//            }
        }

        return false;
    }
}