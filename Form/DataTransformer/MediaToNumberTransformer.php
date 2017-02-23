<?php

namespace Lch\MediaBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Event\ImageEvent;
use Lch\MediaBundle\Event\ReverseTransformEvent;
use Lch\MediaBundle\Event\TransformEvent;
use Lch\MediaBundle\LchMediaEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MediaToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var string
     */
    private $entityReference;

    /**
     * @var array
     */
    private $mediaParameters;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * MediaToNumberTransformer constructor.
     * @param ObjectManager $manager
     * @param EventDispatcherInterface $eventDispatcher
     * @param $entityReference
     * @param $mediaParameters
     */
    public function __construct(ObjectManager $manager, EventDispatcherInterface $eventDispatcher, $entityReference, $mediaParameters)
    {
        $this->manager = $manager;
        $this->entityReference = $entityReference;
        $this->mediaParameters = $mediaParameters;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param mixed $media
     * @return Media|string
     */
    public function transform($media)
    {
        if (null === $media) {
            return '';
        }

        $transformEvent = new TransformEvent($media, $this->mediaParameters);
        $this->eventDispatcher->dispatch(
            LchMediaEvents::TRANSFORM, $transformEvent
        );

        return $transformEvent->getMedia();
    }

    /**
     * Used on form validation, to transform media ID in real entity for relation saving
     * @param mixed $mediaNumber
     * @return null|object
     * @throws \Exception
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

        if(!$media instanceof Media) {
            // TODO specialize exception
            throw new \Exception();
        }

        $reverseTransformEvent = new ReverseTransformEvent($media, $this->mediaParameters);
        $this->eventDispatcher->dispatch(
            LchMediaEvents::REVERSE_TRANSFORM, $reverseTransformEvent
        );

        if (null === $media) {
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $mediaNumber
            ));
        }

        return $reverseTransformEvent->getMedia();
    }
}
