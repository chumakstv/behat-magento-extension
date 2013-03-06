<?php
/**
 * This file is part of the Behat Magento extension.
 * (c) 2013 Vadim Kusakin <vadim.irbis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Irs\BehatMagentoExtension\Context;

use Irs\MagentoInitializer\Installer\InstallerInterface as MagentoInstallerInterface;
use Irs\MagentoInitializer\Initializer\InitializerInterface as MagentoInitializerInterface;
use Behat\Behat\Context\ContextInterface;
use Behat\Behat\Context\Initializer\InitializerInterface;

class MagentoHooksInitializer implements InitializerInterface
{
    /**
     * Magento installer
     *
     * @var MagentoInstallerInterface
     */
    private $_installer;

    /**
     * Magento initializer
     *
     * @var MagentoInitializerInterface
     */
    private $_initializer;


    /**
     * Constructs instance of magento hooks initializer
     *
     * @param MagentoInstallerInterface $installer Magento installer
     * @param MagentoInitializerInterface $initializer Magento initializer
     */
    public function __construct(MagentoInstallerInterface $installer, MagentoInitializerInterface $initializer)
    {
        $this->_installer = $installer;
        $this->_initializer = $initializer;
    }

    /**
     * If constext uses MagentoHooks trait returns true false otherwise
     *
     * @see \Behat\Behat\Context\Initializer\InitializerInterface::supports()
     * @return bool
     */
    public function supports(ContextInterface $context)
    {
        $reflection = new \ReflectionObject($context);
        if (!method_exists($reflection, 'getTraitNames')) {
            return false;
        }

        $traits = $reflection->getTraitNames();
        if (null === $traits) {
            throw new \RuntimeException('Error on retrieving traits from context.');
        }

        return in_array('Irs\BehatMagentoExtension\Context\MagentoHooks', $traits);
    }

    /**
     * Injects Magento installer and initializer into context
     *
     * @see \Behat\Behat\Context\Initializer\InitializerInterface::initialize()
     */
    public function initialize(ContextInterface $context)
    {
        $context->setInstaller($this->_installer);
        $context->setInitializer($this->_initializer);
    }
}
