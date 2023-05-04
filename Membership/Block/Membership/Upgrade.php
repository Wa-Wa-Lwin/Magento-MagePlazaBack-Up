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

namespace Mageplaza\Membership\Block\Membership;

use Magento\Framework\DataObject;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\Config\Source\MembershipStatus;
use Mageplaza\Membership\Model\Config\Source\UpgradingCost;
use Mageplaza\Membership\Model\Membership;

/**
 * Class Upgrade
 * @package Mageplaza\Membership\Block\Membership
 */
class Upgrade extends Page
{
    /**
     * @var bool
     */
    protected $_isUpgradePage = true;

    /**
     * @return DataObject[]|Membership[]
     */
    public function getMembershipCollection()
    {
        if (!$customer = $this->accountHelper->getCurrentCustomer()) {
            return [];
        }

        $membership = $this->membershipFactory->create();
        $this->membershipResource->load($membership, $customer->getGroupId());

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('default_product', ['gt' => 0])
            ->addFieldToFilter('status', MembershipStatus::ACTIVE)
            ->addFieldToFilter('level', ['gt' => $membership->getLevel()])
            ->setOrder('sort_order', 'asc');

        return $collection->getItems();
    }

    /**
     * @return float
     */
    public function getDeductAmount()
    {
        $customer = $this->accountHelper->getCurrentCustomer();

        if (!$customer
            || $this->helper->getUpgradingCost() !== UpgradingCost::DEDUCT_REMAIN
            || (int)$customer->getStatus() !== CustomerStatus::ACTIVE
            || $customer->getDuration() === null
        ) {
            return 0;
        }

        $rate = 1;

        if ($duration = $customer->getDuration()) {
            $remaining = strtotime($customer->getStartDate()) + $duration - time();
            $remaining -= $remaining % 3600;

            $rate = $remaining / $duration;
        }

        return $customer->getMembershipPrice() * $rate;
    }
}
