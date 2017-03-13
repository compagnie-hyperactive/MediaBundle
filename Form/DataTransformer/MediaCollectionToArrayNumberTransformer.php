<?php

namespace Lch\MediaBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Event\ImageEvent;
use Lch\MediaBundle\Event\ReverseTransformEvent;
use Lch\MediaBundle\Event\TransformEvent;
use Lch\MediaBundle\LchMediaEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MediaCollectionToArrayNumberTransformer implements DataTransformerInterface
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
    public function __construct(ObjectManager $manager, EventDispatcherInterface $eventDispatcher, $entityReference, $mediaParameters = [])
    {
        $this->manager = $manager;
        $this->entityReference = $entityReference;
        $this->eventDispatcher = $eventDispatcher;
        $this->mediaParameters = $mediaParameters;
    }

    /**
     * @param mixed $media
     * @return Media|string
     */
    public function transform($medias)
    {
        if (null === $medias) {
            return '';
        }

        if($medias instanceof Collection) {
            $returnCollection = new ArrayCollection();
            foreach ($medias as $media) {
                $transformEvent = new TransformEvent($media, $this->mediaParameters);
                $this->eventDispatcher->dispatch(
                    LchMediaEvents::TRANSFORM, $transformEvent
                );

                $returnCollection->add($transformEvent->getMedia());
            }

            return $returnCollection;
        }
    }

    /**
     * Used on form validation, to transform media ID in real entity for relation saving
     * @param mixed $medias
     * @return null|object
     * @throws \Exception
     */
    public function reverseTransform($medias)
    {
        // TODO add exception
        if (!$medias) {
            return null;
        }

        else {
            $returnCollection = new ArrayCollection();
            foreach ($medias as $media) {
                $reverseTransformEvent = new ReverseTransformEvent($media, $this->mediaParameters);
                $this->eventDispatcher->dispatch(
                    LchMediaEvents::REVERSE_TRANSFORM, $reverseTransformEvent
                );

                $returnCollection->add($reverseTransformEvent->getMedia());
            }
            return $returnCollection;
        }
    }
}
