<?php

namespace Lch\TranslateBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Lch\TranslateBundle\Utils\TranslationsHelper;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Todo: Validation LifeCycleCallback
 *
 * Class TranslatableEventSubscriber
 * @package Lch\TranslateBundle\EventListener
 */
class TranslatableEventSubscriber implements EventSubscriber
{
    /** @var TranslationsHelper $translationsHelper */
    protected $translationsHelper;

    /** @var ValidatorInterface $validator */
    protected $validator;

    /**
     * TranslatableEntityEventSubscriber constructor.
     * @param TranslationsHelper $translationsHelper
     * @param ValidatorInterface $validator
     */
    public function __construct(TranslationsHelper $translationsHelper,
                                ValidatorInterface $validator
    ) {
        $this->translationsHelper = $translationsHelper;
        $this->validator = $validator;
    }

    /**
     * @inheritdoc
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            Events::prePersist,
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $args
     *
     * @return void
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        $metadata = $args->getClassMetadata();
        $class = $metadata->getName();
        if (!$this->translationsHelper->isEntityTranslatable($class)) {
            return;
        }

        if (!array_key_exists('translatedParent', $metadata->getAssociationMappings())) {
            $metadata->mapManyToOne([
                'fieldName'    => 'translatedParent',
                'targetEntity' => $class,
                'inversedBy'   => 'translatedChildren'
            ]);
        }
        if (!array_key_exists('translatedChildren', $metadata->getAssociationMappings())) {
            $metadata->mapOneToMany([
                'fieldName' => 'translatedChildren',
                'targetEntity' => $class,
                'mappedBy' => 'translatedParent'
            ]);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return void
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$this->translationsHelper->isEntityTranslatable($entity)) {
            return;
        }

        if (null === $entity->getTranslatedParent()) {
            return;
        }

        $errors = $this->validator->validate($entity);
        if ($entity->getTranslatedParent()->getId() === $entity->getId()) {
            $errors->add(new ConstraintViolation(
                'Entity cannot be its own translatable parent.',
                '',
                [],
                $entity,
                'translatedParent',
                $entity->getId()
            ));
        }

    }
}
