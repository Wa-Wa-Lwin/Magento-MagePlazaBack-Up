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

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Mageplaza\Membership\Helper\Account;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\ChangeMembership;
use Mageplaza\Membership\Model\Product\Type\Membership;

/**
 * Class OrderSaveAfter
 * @package Mageplaza\Membership\Observer
 */
class OrderSaveAfter implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Account
     */
    protected $accountHelper;

    /**
     * OrderSaveAfter constructor.
     *
     * @param Data $helper
     * @param Account $accountHelper
     */
    public function __construct(
        Data $helper,
        Account $accountHelper
    ) {
        $this->helper = $helper;
        $this->accountHelper = $accountHelper;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        $storeId = $order->getStoreId();

        if ($this->helper->changeMembershipWhen($storeId) !== ChangeMembership::COMPLETE
            || $order->getState() !== Order::STATE_COMPLETE
        ) {
            return $this;
        }

        foreach ($order->getAllItems() as $item) {
            if ($item->isDummy() || ($item->getProductType() !== Membership::TYPE_MEMBERSHIP)) {
                continue;
            }

            $this->accountHelper->buyNewMembership($order, $item);
        }

        return $this;
    }
}
