<?php
/**
 * This file is part of the Behat Magento extension.
 * (c) 2013 Vadim Kusakin <vadim.irbis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Irs\BehatMagentoExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldImplementExtensionInterface()
    {
        $this->assertInstanceOf('Behat\Behat\Extension\ExtensionInterface', new Extension);
    }

    public function testLoadShouldAddPropelyConfiguredHookInitializerToDiContainer()
    {
        // preapre
        $extension = new Extension;
        $container = new ContainerBuilder;
        $config = array(
            'scope'    => uniqid(),
            'store'    => uniqid(),
            'magento'  => uniqid(),
            'target'   => uniqid(),
            'url'      => uniqid(),
            'database' => array(
                'host'     => uniqid(),
                'user'     => uniqid(),
                'password' => uniqid(),
                'schema'   => uniqid(),
            ),
        );

        // act
        $extension->load($config, $container);

        // assert
        $this->assertEquals($config['magento'], $container->getParameter('irs.magento.source.path'));
        $this->assertEquals($config['target'], $container->getParameter('irs.magento.target.path'));
        $this->assertEquals($config['url'], $container->getParameter('irs.magento.url'));
        $this->assertEquals($config['database']['host'], $container->getParameter('irs.magento.db.host'));
        $this->assertEquals($config['database']['user'], $container->getParameter('irs.magento.db.user'));
        $this->assertEquals($config['database']['password'], $container->getParameter('irs.magento.db.password'));
        $this->assertEquals($config['database']['schema'], $container->getParameter('irs.magento.db.schema'));
        $this->assertEquals($config['scope'], $container->getParameter('irs.magento.scope'));
        $this->assertEquals($config['store'], $container->getParameter('irs.magento.store'));
        $this->assertNotEmpty($container->findTaggedServiceIds('behat.context.initializer'));
    }

    public function testShouldReturnEmptyPassList()
    {
        $extension = new Extension;

        $this->assertSame(array(), $extension->getCompilerPasses());
    }

    /**
     * @dataProvider providerCorrectConfigs
     */
    public function testCorrectConfigsShouldFollowDefinition(array $sourceConfig, array $expectedConfig)
    {
        // preapre
        $builder = new TreeBuilder;
        $builder->root('extensions')
            ->append($extensionNode = new ArrayNodeDefinition('\Irs\BehatMagentoExtension\Extension'));
        $processor = new Processor;
        $extension = new Extension;

        // act
        $extension->getConfig($extensionNode);
        $processedConfig = $processor->processConfiguration(new ConfigurationTester($builder), $sourceConfig);

        // assert
        $this->assertEquals($expectedConfig, $processedConfig);
    }

    /**
     * @dataProvider providerIncorrectConfigs
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testIncorrectConfigsShouldNotFollowDefinition(array $sourceConfig)
    {
        // preapre
        $builder = new TreeBuilder;
        $builder->root('extensions')
            ->append($node = new ArrayNodeDefinition('\Irs\BehatMagentoExtension\Extension'));
        $processor = new Processor;
        $extension = new Extension;

        // act
        $extension->getConfig($node);
        $processor->processConfiguration(new ConfigurationTester($builder), $sourceConfig);
    }

    public function providerCorrectConfigs()
    {
        return array(
            array(
                array(
                    'extensions' => array(
                        '\Irs\BehatMagentoExtension\Extension' => array(
                            'magento'  => 'path_to_magento',
                            'target'   => 'test_magento_path',
                            'url'      => 'magento_url',
                            'database' => array(
                                'host'     => 'host',
                                'user'     => 'user',
                                'password' => 'password',
                                'schema'   => 'schema',
                            ),
                        ),
                    ),
                ),
                array(
                    '\Irs\BehatMagentoExtension\Extension' => array(
                        'magento'  => 'path_to_magento',
                        'target'   => 'test_magento_path',
                        'url'      => 'magento_url',
                        'database' => array(
                            'host'     => 'host',
                            'user'     => 'user',
                            'password' => 'password',
                            'schema'   => 'schema',
                        ),
                        'store' => '',
                        'scope' => 'store',
                    ),
                ),
            ),
        );
    }

    public function providerIncorrectConfigs()
    {
        return array(
            array(
                array(
                    'extensions' => array(
                        '\Irs\BehatMagentoExtension\Extension' => array(
                            'magento'  => 'path_to_magento',
                            'database' => array(
                                'host'     => 'host',
                                'user'     => 'user',
                                'password' => 'password',
                                'schema'   => 'schema',
                            ),
                        ),
                    ),
                ),
                array(
                    'extensions' => array(
                        '\Irs\BehatMagentoExtension\Extension' => array(
                            'target'   => 'test_magento_path',
                            'database' => array(
                                'host'     => 'host',
                                'user'     => 'user',
                                'password' => 'password',
                                'schema'   => 'schema',
                            ),
                        ),
                    ),
                ),
                array(
                    'extensions' => array(
                        '\Irs\BehatMagentoExtension\Extension' => array(
                            'magento'  => 'path_to_magento',
                            'target'   => 'test_magento_path',
                        ),
                    ),
                ),
                array(
                    'extensions' => array(
                        '\Irs\BehatMagentoExtension\Extension' => array(
                            'magento'  => 'path_to_magento',
                            'target'   => 'test_magento_path',
                            'database' => array(
                                'password' => 'password',
                                'schema'   => 'schema',
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
}

class ConfigurationTester implements ConfigurationInterface
{
    private $_builder;

    public function __construct(TreeBuilder $builder)
    {
        $this->_builder = $builder;
    }

    public function getConfigTreeBuilder()
    {
        return $this->_builder;
    }
}