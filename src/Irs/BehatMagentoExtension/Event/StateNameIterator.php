<?php
/**
 * This file is part of the Behat Magento extension.
 * (c) 2013 Vadim Kusakin <vadim.irbis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Irs\BehatMagentoExtension\Event;

class StateNameIterator extends \FilterIterator
{
	public function __construct(TagIterator $tagIterator)
	{
		parent::__construct($tagIterator);
	}

	public function accept()
	{
		$tag = $this->getInnerIterator()->current();

 		return 'state:' == substr($tag, 0, 6);
	}

	public function current()
	{
		$tag = parent::current();
		if ($tag) {
			$tag = substr($tag, 6);
		}

		return $tag;
	}
}