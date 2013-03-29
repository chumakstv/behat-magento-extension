<?php
/**
 * This file is part of the Behat Magento extension.
 * (c) 2013 Vadim Kusakin <vadim.irbis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Irs\BehatMagentoExtension\Context;

use Behat\Behat\Context\ContextInterface;

class MagentoHooksInitializerTest extends \PHPUnit_Framework_TestCase
{
    protected function initializerMock()
    {
        return $this->getMock(
            'Irs\MagentoInitializer\Initializer\InitializerInterface',
            array('initialize', 'saveState', 'restoreState')
        );
    }

    protected function installerMock()
    {
        return $this->getMock(
            'Irs\MagentoInitializer\Installer\InstallerInterface',
            array('install', 'isInstalled')
        );
    }

    public function testShouldImplementInitializerInterface()
    {
        $this->assertInstanceOf(
            'Behat\Behat\Context\Initializer\InitializerInterface',
            new MagentoHooksInitializer($this->installerMock(), $this->initializerMock())
        );
    }

    public function testShouldSupportContextWithHooksTrait()
    {
        $initializer = new MagentoHooksInitializer($this->installerMock(), $this->initializerMock());

        $this->assertTrue($initializer->supports(new SupportedContext));
    }

    public function testShouldNotSupportContextWithoutHooksTrait()
    {
        $initializer = new MagentoHooksInitializer($this->installerMock(), $this->initializerMock());

        $this->assertFalse($initializer->supports(new NotSupportedContext));
    }

    public function testShouldInitializeMagentoHooks()
    {
        // preapre
        $magentoInstaller = $this->installerMock();
        $magentoInitializer = $this->initializerMock();
        $initializer = new MagentoHooksInitializer($magentoInstaller, $magentoInitializer);

        $context = $this->getMock('Behat\Behat\Context\ContextInterface', array('setInstaller', 'setInitializer'));
        $context->expects($this->once())
            ->method('setInstaller')
            ->with($magentoInstaller);
        $context->expects($this->once())
            ->method('setInitializer')
            ->with($magentoInitializer);

        // act
        $initializer->initialize($context);
    }
}

class SupportedContext implements ContextInterface
{
    use MagentoHooks;
}

class NotSupportedContext implements ContextInterface
{}
