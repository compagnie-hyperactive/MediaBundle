<?php

namespace Lch\TranslateBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Lch\TranslateBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    public const ROOT_NAMESPACE = 'lch_translate';
    public const ROOT_PARAMETERS_NAMESPACE = 'lch.translate';

    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(self::ROOT_NAMESPACE);

        $rootNode
            ->children()
                ->arrayNode('available_languages')
                    ->scalarPrototype()
                    ->end()
                    ->defaultValue([])
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}