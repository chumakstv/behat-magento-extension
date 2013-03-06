<?php
/**
 * This file is part of the Magento initialization framework.
 * (c) 2013 Vadim Kusakin <vadim.irbis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Irs\BehatMagentoExtension\Context;

use Irs\BehatMagentoExtension\Event\TagIterator;
use Irs\BehatMagentoExtension\Event\StateNameIterator;
use Behat\Behat\Event\EventInterface;
use Irs\MagentoInitializer\Initializer\InitializerInterface;
use Irs\MagentoInitializer\Installer\InstallerInterface;

trait MagentoHooks
{
    /**
     * Magento installer
     *
     * @var InstallerInterface
     */
    private static $_installer;

    /**
     * Magento initializer
     *
     * @var InitializerInterface
     */
    private static $_initializer;

    /**
     * Sets Magento installer
     *
     * @param InstallerInterface $installer
     */
    public static function setInstaller(InstallerInterface $installer)
    {
        self::$_installer = $installer;
    }

    /**
     * Returns installer
     *
     * @return InstallerInterface
     * @throws \DomainException If installer was not injected
     */
    protected static function _getInstaller()
    {
        if (!self::$_installer) {
            throw new \DomainException(
                'Magento installer has not been injected to the context; please turn on extension in behat.yml.'
            );
        }

        return self::$_installer;
    }

    /**
     * Sets Magento initializer
     *
     * @param InitializerInterface $initializer
     */
    public function setInitializer(InitializerInterface $initializer)
    {
        self::$_initializer = $initializer;
    }

    /**
     * Returns initializer
     *
     * @return InitializerInterface
     * @throws \DomainException If initializer was not injected
     */
    protected static function _getInitializer()
    {
        if (!self::$_initializer) {
            throw new \DomainException(
                'Magento initializer has not been injected to the context; please turn on extension in behat.yml.'
            );
        }

        return self::$_initializer;
    }

	/**
     * @BeforeSuite
     */
	public static function prepare()
	{
		$defaultStateName = 'default';

		if (self::_getInstaller()->isInstalled()) {
			self::_restoreStateByName($defaultStateName);
		} else {
			self::_getInstaller()->install();
			self::_saveStateByName($defaultStateName);
		}

		self::_getInitializer()->initialize();
	}

	/**
	 * @BeforeScenario
	 * @BeforeFeature
	 */
	public static function restoreState(EventInterface $event)
	{
		foreach (new StateNameIterator(new TagIterator($event)) as $name) {
			self::_restoreStateByName($name);
			return;
		}
	}

	private static function _restoreStateByName($name)
	{
		$fileName = self::_getStateFileName($name);
		if (!file_exists($fileName)) {
			throw new \DomainException("Unable to load default state; file '$fileName'.");
		}

		self::_getInitializer()->restoreState($fileName);
	}

	private static function _saveStateByName($name)
	{
		$fileName = self::_getStateFileName($name);
		self::_getInitializer()->saveState($fileName);
	}

	/**
     * Returns path to state on file system
     *
     * @param string $name
     * @return string;
	 */
	private static function _getStateFileName($name)
	{
		return 'states' . DIRECTORY_SEPARATOR . "$name.state";
	}
}
