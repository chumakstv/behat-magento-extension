<?php
/**
 * This file is part of the Behat Magento extension.
 * (c) 2013 Vadim Kusakin <vadim.irbis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Irs\BehatMagentoExtension\Event;

use Behat\Behat\Event\OutlineExampleEvent;
use Behat\Behat\Event\FeatureEvent;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Event\EventInterface;

class TagIterator extends \ArrayIterator
{
	public function __construct(EventInterface $event)
	{
		$tags = array();

		if ($event instanceof ScenarioEvent) {
			$tags = $event->getScenario()->getOwnTags();
		} else if ($event instanceof FeatureEvent) {
			$tags = $event->getFeature()->getTags();
		} else if ($event instanceof OutlineExampleEvent) {
			$tags = $event->getOutline()->getOwnTags();
		} else {
			throw new \InvalidArgumentException(get_class($event) . ' is unsupported event type.');
		}

		parent::__construct($tags);
	}
}
