<?php
/**
 * This file is part of the Behat Magento extension.
 * (c) 2013 Vadim Kusakin <vadim.irbis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Irs\BehatMagentoExtension\Context;

use Irs\MagentoInitializer\Initializer\InitializerInterface;
use Irs\MagentoInitializer\Installer\InstallerInterface;
use Behat\Behat\Event\OutlineExampleEvent;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Behat\Event\FeatureEvent;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Gherkin\Node\ScenarioNode;
use Irs\BehatMagentoExtension\Helper;

require_once 'Irs/BehatMagentoExtension/Helper.php';

class MagentoHooksTest extends \PHPUnit_Framework_TestCase
{
	protected $_workingDir;
	protected $_testDir;
	protected $_context;

	protected function setUp()
	{
		$this->_context = $this->getObjectForTrait('Irs\BehatMagentoExtension\Context\MagentoHooks');
		$this->_workingDir = Helper::createTempDir();
		$this->_testDir = getcwd();
		chdir($this->_workingDir);
		mkdir('states');
	}

	protected function tearDown()
	{
		chdir($this->_testDir);
		Helper::delete($this->_workingDir);
	}

	public function testShouldInitializeIfMagentoInstalled()
	{
		// prepare
		$defaultStatePath = 'states' . DIRECTORY_SEPARATOR . 'default.state';

		$installer = $this->installerMock(true);
		$installer->expects($this->never())
			->method('install');

		$initializer = $this->initializerMock();
		$initializer->expects($this->once())
			->method('initialize');
		$initializer->expects($this->once())
			->method('restoreState')
			->with($defaultStatePath);

		touch($defaultStatePath);

		// act
		$this->_context->setInstaller($installer);
		$this->_context->setInitializer($initializer);
		$this->_context->prepare();
	}

	/**
	 * @expectedException DomainException
	 */
	public function testShouldThrowDomainExceptionIfMagentoInstalledButDefaultStateDoesNotExist()
	{
		// prepare
		$installer = $this->installerMock(true);
		$installer->expects($this->never())
			->method('install');

		// act
		$this->_context->setInstaller($installer);
		$this->_context->prepare();
	}

	public function testShouldInstallMagentoIfNotInstalledAndSaveDefaultState()
	{
		// prepare
		$installer = $this->installerMock(false);
		$installer->expects($this->once())
			->method('install');

		$initializer = $this->initializerMock();
		$initializer->expects($this->once())
			->method('initialize');
		$initializer->expects($this->once())
			->method('saveState')
			->with('states' . DIRECTORY_SEPARATOR . 'default.state');

		// act
		$this->_context->setInstaller($installer);
		$this->_context->setInitializer($initializer);
		$this->_context->prepare();
	}

	/**
	 * @dataProvider providerTaggedEvents
	 */
	public function testShouldRestoreState($event, $expectedTagPath)
	{
		// prepare
		$initializer = $this->initializerMock();
		$initializer->expects($this->once())
			->method('restoreState')
			->with($expectedTagPath);

		touch($expectedTagPath);

		// act
		$this->_context->setInitializer($initializer);
		$this->_context->restoreState($event);
	}

	/**
	 * @dataProvider providerTaggedEvents
	 * @expectedException DomainException
	 */
	public function testShouldThrowDomainExceptionOnRestoringFromIncorrectState($event)
	{
		// prepare
		$initializer = $this->initializerMock();
		$initializer->expects($this->never())
			->method('restoreState');

		// act
		$this->_context->setInitializer($initializer);
		$this->_context->restoreState($event);
	}

	/**
	 * @expectedException DomainException
	 */
	public function testGetInstallerShouldThrowDomainExceptionIfWasNotInjected()
	{
	    $this->_context->prepare();
	}

	/**
	 * @expectedException DomainException
	 */
	public function testGetInitializerShouldThrowDomainExceptionIfWasNotInjected()
	{
	    $this->_context->setInstaller($this->installerMock(true));
	    $this->_context->prepare();
	}

	public function providerTaggedEvents()
	{
		return array(
			array($this->scenarioEventMock('state:abc'), 'states' . DIRECTORY_SEPARATOR . 'abc.state'),
			array($this->featureEventMock('state:asd'), 'states' . DIRECTORY_SEPARATOR . 'asd.state'),
			array($this->outlineExampleEventMock('state:zxc'), 'states' . DIRECTORY_SEPARATOR . 'zxc.state'),
		);
	}

	protected function scenarioEventMock($tag)
	{
		$scenario = new ScenarioNode;
		$scenario->addTag($tag);

		return new ScenarioEvent($scenario, $this->getMock('Behat\Behat\Context\ContextInterface'));
	}

	protected function featureEventMock($tag)
	{
		$feature = new FeatureNode;
		$feature->addTag($tag);

		return new FeatureEvent($feature, null);
	}

	protected function outlineExampleEventMock($tag)
	{
		$outline = new OutlineNode;
		$outline->addTag($tag);

		return new OutlineExampleEvent($outline, 0, $this->getMock('Behat\Behat\Context\ContextInterface'));
	}

	protected function installerMock($isInstalled)
	{
		$installer = $this->getMock('Irs\MagentoInitializer\Installer\InstallerInterface', array('install', 'isInstalled'));
		$installer->expects($this->any())
			->method('isInstalled')
			->will($this->returnValue($isInstalled));

		return $installer;
	}

	protected function initializerMock()
	{
		return $this->getMock('Irs\MagentoInitializer\Initializer\InitializerInterface');
	}
}
