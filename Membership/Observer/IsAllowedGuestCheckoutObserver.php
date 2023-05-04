<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Membership
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Membership\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\Membership\Model\Product\Type\Membership;

/**
 * Class IsAllowedGuestCheckoutObserver
 * @package Mageplaza\Membership\Observer
 */
class IsAllowedGuestCheckoutObserver implements ObserverInterface
{
    /**
     * Check is allowed guest checkout if quote contain membership product(s)
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $result = $observer->getEvent()->getResult();

        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            if (($product = $item->getProduct()) && $product->getTypeId() === Membership::TYPE_MEMBERSHIP) {
                $result->setIsAllowed(false);
                break;
            }
        }

        return $this;
    }
}
