<?php

namespace Lch\MediaBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Lch\MediaBundle\Event\ImageEvent;
use Lch\MediaBundle\LchMediaEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MediaToNumberTransformer implements DataTransformerInterface
{
    private $manager;
    private $entityReference;
    private $mediaParameters;
    private $eventDispatcher;

    public function __construct(ObjectManager $manager, EventDispatcherInterface $eventDispatcher, $entityReference, $mediaParameters)
    {
        $this->manager = $manager;
        $this->entityReference = $entityReference;
        $this->mediaParameters = $mediaParameters;
        $this->eventDispatcher = $eventDispatcher;
    }
    
    public function transform($media)
    {
        if (null === $media) {
            return '';
        }

        return $media;
    }

    /**
     * Used on form validation, to transform media ID in real entity for relation saving
     * @param mixed $mediaNumber
     * @return null|object
     */
    public function reverseTransform($mediaNumber)
    {
        if (!$mediaNumber) {
            return null;
        }
        
        $media = $this->manager
            ->getRepository($this->entityReference)
            ->find($mediaNumber)
        ;

        // TODO dispatch generic event
//        $imageEvent = new ImageEvent($image, $this->mediaParameters);
//
//        $this->eventDispatcher->dispatch(
//            LchMediaEvents::LCH_MEDIA_IMAGE_REVERSE_TRANSFORM, $imageEvent
//        );

        if (null === $media) {
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $mediaNumber
            ));
        }

        return $media;
    }
}
