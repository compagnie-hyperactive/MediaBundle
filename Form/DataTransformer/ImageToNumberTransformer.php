<?php

namespace LCH\MediaBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Lch\MediaBundle\Event\ImageEvent;
use Lch\MediaBundle\LCHMediaEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ImageToNumberTransformer implements DataTransformerInterface
{
    private $manager;
    private $entityReference;
    private $image_param;
    private $eventDispatcher;

    public function __construct(ObjectManager $manager, EventDispatcherInterface $eventDispatcher, $entity_reference, $image_param)
    {
        $this->manager = $manager;
        $this->entityReference = $entity_reference;
        $this->image_param = $image_param;
        $this->eventDispatcher = $eventDispatcher;
    }
    
    public function transform($image)
    {
        if (null === $image) {
            return '';
        }

        return $image;
    }
    
    public function reverseTransform($imageNumber)
    {
        if (!$imageNumber) {
            return null;
        }
        
        $image = $this->manager
            ->getRepository($this->entityReference)
            ->findOneBy(array('id'=>$imageNumber))
        ;

        $imageEvent = new ImageEvent($image, $this->image_param);

        $this->eventDispatcher->dispatch(
            LchMediaEvents::LCH_MEDIA_IMAGE_REVERSE_TRANSFORM, $imageEvent
        );

        if (null === $image) {
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $imageNumber
            ));
        }

        return $image;
    }
}
