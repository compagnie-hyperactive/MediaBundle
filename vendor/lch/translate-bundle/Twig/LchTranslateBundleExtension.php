<?php

namespace Lch\TranslateBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class LchTranslateBundleExtension
 * @package Lch\TranslateBundle\Twig
 */
class LchTranslateBundleExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('available_i18n_paths', [LchTranslateBundleRuntime::class, 'getAvailableI18nPaths']),
            new TwigFunction('translated_path', [LchTranslateBundleRuntime::class, 'getTranslatedPath'])
        ];
    }
}
