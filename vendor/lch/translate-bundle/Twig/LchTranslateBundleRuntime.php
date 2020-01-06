<?php

namespace Lch\TranslateBundle\Twig;

use Lch\TranslateBundle\Utils\LangSwitchHelper;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Class LchTranslateBundleRuntime
 * @package Lch\TranslateBundle\Twig
 */
class LchTranslateBundleRuntime implements RuntimeExtensionInterface
{
    /** @var LangSwitchHelper $langSwitchHelper */
    protected $langSwitchHelper;

    /**
     * LchTranslateBundleRuntime constructor.
     * @param LangSwitchHelper $langSwitchHelper
     */
    public function __construct(LangSwitchHelper $langSwitchHelper)
    {
        $this->langSwitchHelper = $langSwitchHelper;
    }

    /**
     * @param object|null $translatableEntity
     * @param array $parameters
     *
     * @return array
     */
    public function getAvailableI18nPaths(object $translatableEntity = null, $parameters = []): array
    {
        return $this->langSwitchHelper->getAvailableI18nPaths($translatableEntity, $parameters);
    }

    /**
     * @param string $route
     * @param array $parameters
     * @param bool $full Wether to merge query params with route parameters
     *
     * @return string
     */
    public function getTranslatedPath(string $route, array $parameters, $full = false): string
    {
        return $this->langSwitchHelper->getTranslatedPath($route, $parameters, $full);
    }
}
