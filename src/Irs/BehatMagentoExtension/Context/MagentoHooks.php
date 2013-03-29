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
    private static $installer;

    /**
     * Magento initializer
     *
     * @var InitializerInterface
     */
    private static $initializer;

    /**
     * Sets Magento installer
     *
     * @param InstallerInterface $installer
     */
    public static function setInstaller(InstallerInterface $installer)
    {
        self::$installer = $installer;
    }

    /**
     * Returns installer
     *
     * @return InstallerInterface
     * @throws \DomainException If installer was not injected
     */
    protected static function getInstaller()
    {
        if (!self::$installer) {
            throw new \DomainException(
                'Magento installer has not been injected to the context; please turn on extension in behat.yml.'
            );
        }

        return self::$installer;
    }

    /**
     * Sets Magento initializer
     *
     * @param InitializerInterface $initializer
     */
    public function setInitializer(InitializerInterface $initializer)
    {
        self::$initializer = $initializer;
    }

    /**
     * Returns initializer
     *
     * @return InitializerInterface
     * @throws \DomainException If initializer was not injected
     */
    protected static function getInitializer()
    {
        if (!self::$initializer) {
            throw new \DomainException(
                'Magento initializer has not been injected to the context; please turn on extension in behat.yml.'
            );
        }

        return self::$initializer;
    }

	/**
     * @BeforeSuite
     */
	public static function prepare()
	{
		$defaultStateName = 'default';

		if (self::getInstaller()->isInstalled()) {
			self::restoreStateByName($defaultStateName);
		} else {
			self::getInstaller()->install();
			self::saveStateByName($defaultStateName);
		}

		self::getInitializer()->initialize();
	}

	/**
	 * @BeforeScenario
	 * @BeforeFeature
	 */
	public static function restoreState(EventInterface $event)
	{
		foreach (new StateNameIterator(new TagIterator($event)) as $name) {
			self::restoreStateByName($name);
			return;
		}
	}

	private static function restoreStateByName($name)
	{
		$fileName = self::getStateFileName($name);
		if (!file_exists($fileName)) {
			throw new \DomainException("Unable to load default state; file '$fileName'.");
		}

		self::getInitializer()->restoreState($fileName);
	}

	private static function saveStateByName($name)
	{
		$fileName = self::getStateFileName($name);
		self::getInitializer()->saveState($fileName);
	}

	/**
     * Returns path to state on file system
     *
     * @param string $name
     * @return string;
	 */
	private static function getStateFileName($name)
	{
		return 'states' . DIRECTORY_SEPARATOR . "$name.state";
	}
}
