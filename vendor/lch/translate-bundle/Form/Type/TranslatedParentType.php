<?php

namespace Lch\TranslateBundle\Form\Type;

use Lch\TranslateBundle\Event\GuessingTranslatedParentLabelEvent;
use Lch\TranslateBundle\Event\LchTranslateBundleEvents;
use Lch\TranslateBundle\Event\QueryingTranslatedParentEvent;
use Lch\TranslateBundle\Utils\TranslationsHelper;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TranslatedParentType
 * @package Lch\TranslateBundle\Form\Type
 */
class TranslatedParentType extends AbstractType
{
    /** @var TranslationsHelper $translationsHelper */
    protected $translationsHelper;

    /** @var RequestStack $requestStack */
    protected $requestStack;

    /** @var EventDispatcherInterface $eventDispatcher */
    protected $eventDispatcher;

    /**
     * ContentReferenceType constructor.
     *
     * @param TranslationsHelper       $translationsHelper
     * @param RequestStack             $requestStack
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(TranslationsHelper $translationsHelper,
                                RequestStack $requestStack,
                                EventDispatcherInterface $eventDispatcher
    ) {
        $this->translationsHelper = $translationsHelper;
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => null,
            'required' => false,
            'choice_label' => function ($entity) {
                $label = $entity->getId();
                $this->eventDispatcher->dispatch(
                    LchTranslateBundleEvents::GUESSING_TRANSLATED_PARENT_LABEL,
                    new GuessingTranslatedParentLabelEvent($label, $entity)
                );

                return $label;
            },
            // Todo: Improve query
            'query_builder' => function (EntityRepository $er) {
                $qb = $er
                    ->createQueryBuilder('entity')
                    ->select('entity')
                    ->where('entity.translatedParent IS NULL')
//                        ->where('entity.language = :locale')
//                        ->setParameter(
//                            'locale',
//                            $this->requestStack->getCurrentRequest()->getLocale()
//                        )
                    ->orderBy('entity.title', 'ASC')
                ;

                $this->eventDispatcher->dispatch(
                    LchTranslateBundleEvents::QUERYING_TRANSLATED_PARENT,
                    new QueryingTranslatedParentEvent($qb)
                );

                return $qb;
            },
            'attr' => [
                'class' => 'translated-parent-choice-field'
            ]
        ]);
    }

    /**
     * @return string
     */
    public function getParent(): string
    {
        return EntityType::class;
    }
}
