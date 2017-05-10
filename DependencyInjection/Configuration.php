<?php

namespace Lch\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    const ROOT_NAMESPACE = "lch_media";
    const ROOT_PARAMETERS_NAMESPACE = "lch.media";
    const KEY = 'key';
    const DEFAULT_VALUE = 'default_value';
    const ROOT_FOLDER = [
        self::KEY           => 'root_folder',
        self::DEFAULT_VALUE => 'uploads'
    ];

    const TYPES = 'types';

    const ENTITY = 'entity';
    const NAME = 'name';
    const FORM = 'form';
    const ADD_VIEW = 'add_view';
    const THUMBNAIL_VIEW = 'thumbnail_view';
    const LIST_ITEM_VIEW = 'list_item_view';
    const SEARCH_FORM_VIEW = 'search_form_view';
    const EXTENSIONS = 'extensions';
    const THUMBNAIL_SIZES = 'thumbnail_sizes';
    const WIDTH = 'width';
    const HEIGHT = 'height';

    const STRATEGY = 'strategy';
    const RESIZE_STRATEGY = 'resize';
    const CROP_STRATEGY = 'crop';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(static::ROOT_NAMESPACE);

        // TODO add other options
        $rootNode
            // Root folder for medias
            ->children()
                ->scalarNode(static::ROOT_FOLDER[static::KEY])
                    ->defaultValue(static::ROOT_FOLDER[static::DEFAULT_VALUE])
                    ->info('Define the relative media root dir')
                ->end()
                ->arrayNode(static::TYPES)
                    ->prototype('array')
                        ->children()
                            ->scalarNode(static::NAME)
                                ->isRequired()
                            ->end()
                            ->scalarNode(static::ENTITY)
                                ->isRequired()
                                // TODO add check class exists ifTrue
                            ->end()
                            ->scalarNode(static::FORM)
                                // TODO add check class exists ifTrue
                            ->end()
                            ->scalarNode(static::ADD_VIEW)
                                // TODO add check twig exists ifTrue
                            ->end()
                            ->scalarNode(static::THUMBNAIL_VIEW)
                                // TODO add check twig exists ifTrue
                            ->end()
                            ->scalarNode(static::LIST_ITEM_VIEW)
                                // TODO add check twig exists ifTrue
                            ->end()
                            ->scalarNode(static::SEARCH_FORM_VIEW)
                                // TODO add check twig exists ifTrue
                            ->end()
                            ->arrayNode(static::EXTENSIONS)
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode(static::THUMBNAIL_SIZES)
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode(static::WIDTH)
                                            ->isRequired()
                                        ->end()
                                        ->scalarNode(static::HEIGHT)
                                            ->isRequired()
                                        ->end()
                                        ->scalarNode(static::STRATEGY)
                                            ->defaultValue(static::RESIZE_STRATEGY)
                                            ->validate()
                                            ->ifNotInArray(array(static::RESIZE_STRATEGY, static::CROP_STRATEGY))
                                                ->thenInvalid('Invalid image strategy specified: %s')
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

        ;
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
