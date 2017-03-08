<?php

namespace Lch\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class LchMediaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        /*******************************
         * Add configuration parameters
         */
        // Media root folder
        if(isset($config[Configuration::ROOT_FOLDER[Configuration::KEY]])) {
            $container->setParameter(
                Configuration::ROOT_NAMESPACE . "." . Configuration::ROOT_FOLDER[Configuration::KEY],
                $config[Configuration::ROOT_FOLDER[Configuration::KEY]]
            );
        }

        // Overload registered types and declare new ones
        if(isset($config[Configuration::TYPES])) {
            $typesParametersAlias = Configuration::ROOT_PARAMETERS_NAMESPACE . "." . Configuration::TYPES;
            $container->setParameter($typesParametersAlias, array_merge($container->getParameter($typesParametersAlias), $config[Configuration::TYPES]));
        }

    }
}
