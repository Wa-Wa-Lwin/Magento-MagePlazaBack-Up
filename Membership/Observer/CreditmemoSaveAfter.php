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

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Item;
use Mageplaza\Membership\Helper\Account;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\CustomerFactory as MembershipCustomerFactory;
use Mageplaza\Membership\Model\Product\Type\Membership;
use Mageplaza\Membership\Model\ResourceModel\Customer as MembershipCustomerResource;

/**
 * Class CreditmemoSaveAfter
 * @package Mageplaza\Membership\Observer
 */
class CreditmemoSaveAfter implements ObserverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CustomerResource
     */
    private $customerResource;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var MembershipCustomerFactory
     */
    private $membershipCustomerFactory;

    /**
     * @var MembershipCustomerResource
     */
    private $membershipCustomerResource;

    /**
     * @var Account
     */
    private $accountHelper;

    /**
     * CreditmemoSaveAfter constructor.
     *
     * @param CustomerFactory $customerFactory
     * @param CustomerResource $customerResource
     * @param MembershipCustomerFactory $membershipCustomerFactory
     * @param MembershipCustomerResource $membershipCustomerResource
     * @param OrderRepositoryInterface $orderRepository
     * @param Account $accountHelper
     */
    public function __construct(
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        MembershipCustomerFactory $membershipCustomerFactory,
        MembershipCustomerResource $membershipCustomerResource,
        OrderRepositoryInterface $orderRepository,
        Account $accountHelper
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->membershipCustomerFactory = $membershipCustomerFactory;
        $this->membershipCustomerResource = $membershipCustomerResource;
        $this->orderRepository = $orderRepository;
        $this->accountHelper = $accountHelper;
    }

    /**
     * @param EventObserver $observer
     *
     * @return $this|void
     * @throws AlreadyExistsException
     */
    public function execute(EventObserver $observer)
    {
        /** @var Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $this->orderRepository->get($creditmemo->getOrderId());

        if ($customerId = $order->getCustomerId()) {
            /** @var CreditmemoItem $item */
            foreach ($creditmemo->getAllItems() as $item) {
                /** @var Item $orderItem */
                $orderItem = $item->getOrderItem();
                $membershipCustomer = $this->membershipCustomerFactory->create();
                $this->membershipCustomerResource->load($membershipCustomer, $customerId);
                $membership = $this->accountHelper->getOptionValue(FieldRenderer::MEMBERSHIP, $orderItem);

                if ($orderItem->getProductType() === Membership::TYPE_MEMBERSHIP && $membershipCustomer->getLastMembershipId() === $membership) {
                    $membershipCustomer->addData([
                        'last_membership_id' => $membershipCustomer->getOldMembershipId(),
                        'start_date' => $membershipCustomer->getOldStartDate(),
                        'duration' => $membershipCustomer->getOldDuration(),
                    ]);
                    $this->membershipCustomerResource->save($membershipCustomer);

                    $customer = $this->customerFactory->create();
                    $this->customerResource->load($customer, $customerId);
                    $customer->setGroupId($membershipCustomer->getOldMembershipId());
                    $this->customerResource->save($customer);
                }
            }
        }

        return $this;
    }
}
