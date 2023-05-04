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
use Magento\Sales\Model\Order\Item;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\Config\Source\HistoryAction;
use Mageplaza\Membership\Model\CustomerFactory;
use Mageplaza\Membership\Model\HistoryFactory;
use Mageplaza\Membership\Model\Product\Type\Membership;
use Mageplaza\Membership\Model\ResourceModel\Customer as CustomerResource;
use Mageplaza\Membership\Model\ResourceModel\History as HistoryResource;

/**
 * Class QuoteSubmitSuccess
 * @package Mageplaza\Membership\Observer
 */
class QuoteSubmitSuccess implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var HistoryResource
     */
    protected $historyResource;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerResource
     */
    protected $customerResource;

    /**
     * QuoteSubmitSuccess constructor.
     *
     * @param Data $helper
     * @param HistoryFactory $historyFactory
     * @param HistoryResource $historyResource
     * @param CustomerFactory $customerFactory
     * @param CustomerResource $customerResource
     */
    public function __construct(
        Data $helper,
        HistoryFactory $historyFactory,
        HistoryResource $historyResource,
        CustomerFactory $customerFactory,
        CustomerResource $customerResource
    ) {
        $this->helper = $helper;
        $this->historyFactory = $historyFactory;
        $this->historyResource = $historyResource;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
    }

    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        $orderStatus = $order->getStatus();
        $orderState = $order->getState();

        $customerId = $order->getCustomerId();

        /** @var Item $item */
        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() !== Membership::TYPE_MEMBERSHIP) {
                continue;
            }

            $action = $this->helper->getOptionValue(FieldRenderer::ACTION, $item) ?: HistoryAction::BUY_NEW;

            $historyData = [
                'item_id' => $item->getId(),
                'customer_id' => $customerId,
                'action' => $action,
                'membership_id' => $this->helper->getOptionValue(FieldRenderer::MEMBERSHIP, $item),
                'amount' => $this->helper->getOptionValue(FieldRenderer::PRICE, $item),
                'membership_data' => $this->helper->getOptionValue(FieldRenderer::DURATION, $item)
            ];

            $history = $this->historyFactory->create();
            $history->addData($historyData);
            $this->historyResource->save($history);

            $customerData = [
                'inactive_membership_id' => $this->helper->getOptionValue(FieldRenderer::MEMBERSHIP, $item)
            ];

            $customer = $this->customerFactory->create();
            $this->customerResource->load($customer, $customerId);

            if (($orderStatus === 'pending' || $orderState === Order::STATE_NEW)
                && (int)$customer->getStatus() !== CustomerStatus::ACTIVE
            ) {
                $customerData['status'] = CustomerStatus::INACTIVE;
            }

            $customer->saveCustomData($customerId, $customerData);
        }
    }
}
