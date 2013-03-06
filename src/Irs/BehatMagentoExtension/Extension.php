<?php
/**
 * This file is part of the Behat Magento extension.
 * (c) 2013 Vadim Kusakin <vadim.irbis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Irs\BehatMagentoExtension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Behat\Behat\Extension\ExtensionInterface;

/**
 * Behat extension
 */
class Extension implements ExtensionInterface
{
    /**
     * Loads services definition to DI container
     *
     * @see \Behat\Behat\Extension\ExtensionInterface::load()
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('services.xml');

        $container->setParameter('irs.magento.source.path', $config['magento']);
        $container->setParameter('irs.magento.target.path', $config['target']);
        $container->setParameter('irs.magento.db.schema', $config['database']['schema']);

        if (isset($config['scope'])) {
            $container->setParameter('irs.magento.scope', $config['scope']);
        }
        if (isset($config['store'])) {
            $container->setParameter('irs.magento.store', $config['store']);
        }
        if (isset($config['database']['password'])) {
            $container->setParameter('irs.magento.db.password', $config['database']['password']);
        }
        if (isset($config['database']['host'])) {
            $container->setParameter('irs.magento.db.host', $config['database']['host']);
        }
        if (isset($config['database']['user'])) {
            $container->setParameter('irs.magento.db.user', $config['database']['user']);
        }
    }

    /**
     * Initializes config definition
     *
     * @see \Behat\Behat\Extension\ExtensionInterface::getConfig()
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('store')
                    ->defaultValue('')
                    ->end()
                ->scalarNode('scope')
                    ->defaultValue('store')
                    ->end()
                ->scalarNode('magento')
                    ->isRequired()
                    ->end()
                ->scalarNode('target')
                    ->isRequired()
                    ->end()
                ->arrayNode('database')
                    ->isRequired()
                    ->children()
                        ->scalarNode('host')
                            ->end()
                        ->scalarNode('user')
                            ->end()
                        ->scalarNode('password')
                            ->end()
                        ->scalarNode('schema')
                            ->isRequired()
                            ->end()
                        ->end()
                    ->end();
    }

    /**
     * Returns compiler passes
     *
     * @see \Behat\Behat\Extension\ExtensionInterface::getCompilerPasses()
     */
    public function getCompilerPasses()
    {
        return array();
    }
}
