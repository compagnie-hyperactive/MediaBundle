<?php

namespace Lch\TranslateBundle\Utils;

use Lch\TranslateBundle\Model\Behavior\Translatable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Class TranslationsHelper
 * @package LchTranslateBundle\Utils
 */
class TranslationsHelper
{
    /** @var ParameterBagInterface $params */
    protected $params;

    /** @var EntityManagerInterface $em */
    protected $em;

    /** @var array $availableLanguages */
    protected $availableLanguages;

    /**
     * TranslationsHelper constructor.
     * @param ParameterBagInterface $params
     * @param EntityManagerInterface $em
     */
    public function __construct(ParameterBagInterface $params,
                                EntityManagerInterface $em
    )
    {
        $this->params = $params;
        $this->em = $em;
    }

    /**
     * Available languages as defined in application parameters.
     *
     * @return array
     */
    public function getAvailableLanguages(): array
    {
        if (!$this->availableLanguages) {
            $this->availableLanguages = $this->params->get('lch.translate.available_languages');
        }

        return $this->availableLanguages;
    }

    /**
     * Checks if Translate bundle system is available,
     * through checking if `available_languages` parameter
     * is set and not empty.
     *
     * @return bool
     */
    public function isTranslationSystemEnabled(): bool
    {
        $availableLanguages = $this->getAvailableLanguages();

        return is_array($availableLanguages) && !empty($availableLanguages);
    }

    /**
     * Checks if given object or class uses Translatable trait.
     *
     * @param object|string object
     *
     * @return bool
     *
     * @throws UnexpectedValueException
     */
    public function isEntityTranslatable($object): bool
    {
        $class = is_object($object) ? get_class($object) : $object;
        if (!is_string($class)) {
            throw new UnexpectedValueException($class, 'string');
        }
        return class_exists($class) && in_array(
                Translatable::class,
                $this->classUsesRecursive($class),
                true
            );
    }

    /**
     * @param $class
     * @param bool $autoload
     * @return array
     */
    protected function classUsesRecursive($class, $autoload = true)
    {
        $traits = [];
        // Get traits of all parent classes
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));
        // Get traits of all parent traits
        $traitsToSearch = $traits;
        while (!empty($traitsToSearch)) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        };
        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }
        return array_unique($traits);
    }
}
