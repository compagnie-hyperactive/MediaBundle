<?php

namespace Lch\TranslateBundle\Form\Type;

use Lch\TranslateBundle\Utils\TranslationsHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LanguageType
 * @package Lch\TranslateBundle\Form\Type
 */
class LanguageType extends AbstractType
{
    /** @var TranslationsHelper $translationsHelper */
    protected $translationsHelper;

    /** @var RequestStack $requestStack */
    protected $requestStack;

    /**
     * LanguageType constructor.
     * @param TranslationsHelper $translationsHelper
     * @param RequestStack $requestStack
     */
    public function __construct(TranslationsHelper $translationsHelper, RequestStack $requestStack)
    {
        $this->translationsHelper = $translationsHelper;
        $this->requestStack = $requestStack;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $availableLanguages = $this->translationsHelper->getAvailableLanguages();
        $resolver->setDefaults([
            'choices' => $availableLanguages
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}