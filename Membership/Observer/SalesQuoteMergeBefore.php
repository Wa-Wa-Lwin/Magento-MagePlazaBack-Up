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
use Magento\Framework\Model\AbstractModel;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\CustomerFactory;
use Mageplaza\Membership\Model\Product\Type\Membership;
use Mageplaza\Membership\Model\ResourceModel\Customer as CustomerResource;

/**
 * Class SalesQuoteMergeBefore
 * @package Mageplaza\Membership\Observer
 */
class SalesQuoteMergeBefore implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerResource
     */
    protected $customerResource;

    /**
     * SalesQuoteMergeBefore constructor.
     *
     * @param Data $helper
     * @param CustomerFactory $customerFactory
     * @param CustomerResource $customerResource
     */
    public function __construct(
        Data $helper,
        CustomerFactory $customerFactory,
        CustomerResource $customerResource
    ) {
        $this->helper = $helper;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        /** @var Quote $source */
        $source = $observer->getEvent()->getSource();

        if (!($quote instanceof AbstractModel)) {
            return $this;
        }

        $found = false;
        /** @var Item $quoteItem */
        foreach ($quote->getAllVisibleItems() as $item) {
            if ($item->getProductType() === Membership::TYPE_MEMBERSHIP) {
                $found = true;
            }
        }

        $customer = $this->customerFactory->create();
        $this->customerResource->load($customer, $quote->getCustomerId() ?: $source->getCustomerId());

        $isAllowOverride = $this->helper->isAllowOverride($source->getStoreId());
        $customerStatus = (int)$customer->getStatus() === CustomerStatus::ACTIVE;

        /**
         * if customer cart has membership products
         * of customer is not allowed to override membership
         * remove membership products from source cart
         */
        if ($found || (!$isAllowOverride && $customerStatus)) {
            foreach ($source->getAllVisibleItems() as $item) {
                if ($item->getProductType() === Membership::TYPE_MEMBERSHIP) {
                    $source->removeItem($item->getId());
                }
            }
        }

        return $this;
    }
}
