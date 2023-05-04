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
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\CustomerFactory;

/**
 * Class CustomerLoadAfter
 * @package Mageplaza\Membership\Observer
 */
class CustomerLoadAfter implements ObserverInterface
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerResource
     */
    protected $customerResource;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * CustomerLoadAfter constructor.
     *
     * @param CustomerFactory $customerFactory
     * @param CustomerResource $customerResource
     * @param Data $helper
     */
    public function __construct(
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        Data $helper
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->helper = $helper;
    }

    /**
     * After load observer for customer
     *
     * @param Observer $observer
     *
     * @return $this
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Customer $object */
        $object = $observer->getEvent()->getCustomer();
        if (!$object instanceof AbstractModel) {
            return $this;
        }

        $customer = $this->customerFactory->create();
        $customer->getResource()->load($customer, $object->getId());
        $customer->attachCustomData($object);

        $start = $customer->getStartDate();
        $duration = (int)$customer->getDuration();
        if (!$start || !$duration || (int)$customer->getStatus() !== CustomerStatus::ACTIVE) {
            return $this;
        }

        if (strtotime($start) + $duration <= time()) {
            $data = [
                'last_membership_id' => $object->getGroupId(),
                'status' => CustomerStatus::EXPIRED,
                'group_id' => $this->helper->getDefaultGroup()
            ];

            $customer->saveCustomData($object->getId(), $data);
            $object->addData($data);
            $this->customerResource->save($object);
        }

        return $this;
    }
}
