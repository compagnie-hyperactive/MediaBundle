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
    const KEY = 'key';
    const DEFAULT_VALUE = 'default_value';
    const ROOT_FOLDER = [
        self::KEY           => 'root_folder',
        self::DEFAULT_VALUE => 'uploads'
    ];

    const ENTITY = 'entity';
    const FORM = 'form';
    const ADD_VIEW = 'add_view';
    const THUMBNAIL_VIEW = 'thumbnail_view';
    const EXTENSIONS = 'extensions';
    const VIEW_TRANSFORMER = 'view_transformer';
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(self::ROOT_NAMESPACE);

        // TODO add other options
        $rootNode
            // Root folder for medias
            ->children()
                ->scalarNode(self::ROOT_FOLDER[self::KEY])
                    ->defaultValue(self::ROOT_FOLDER[self::DEFAULT_VALUE])
                    ->info('Define the relative media root dir')
                ->end()
        ;
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
