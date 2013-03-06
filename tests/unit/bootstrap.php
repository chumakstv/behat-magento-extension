<?php
/**
 * This file is part of the Behat Magento extension.
 * (c) 2013 Vadim Kusakin <vadim.irbis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

spl_autoload_register(function ($className) {
    if (false !== strpos($className, 'Irs\BehatMagentoExtension')) {
        require_once(__DIR__ . '/../../src/' . str_replace('\\', '/', $className) . '.php');
        return true;
    }
}, true, false);

$vendorAutoload = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($vendorAutoload)) {
    throw new RuntimeException('Dependencies is not installed; run "composer install" from project root.');
}

require_once $vendorAutoload;

require_once __DIR__ . '/Irs/BehatMagentoExtension/Helper.php';