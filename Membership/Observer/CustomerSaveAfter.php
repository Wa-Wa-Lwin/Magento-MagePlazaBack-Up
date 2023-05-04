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
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\Customer;
use Mageplaza\Membership\Model\CustomerFactory;

/**
 * Class CustomerSaveAfter
 * @package Mageplaza\Membership\Observer
 */
class CustomerSaveAfter implements ObserverInterface
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * CustomerSaveAfter constructor.
     *
     * @param CustomerFactory $customerFactory
     * @param DateTime $date
     */
    public function __construct(
        CustomerFactory $customerFactory,
        DateTime $date
    ) {
        $this->customerFactory = $customerFactory;
        $this->date = $date;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Customer\Model\Data\Customer $object */
        $object = $observer->getEvent()->getCustomer();

        $customerId = $object->getId();

        /** @var Customer $customer */
        $customer = $this->customerFactory->create();
        $customer->getResource()->load($customer, $customerId);

        if ($object instanceof AbstractModel) {
            $customer->saveCustomData($customerId, $object->getData());

            return $this;
        }

        /** @var Http $request */
        if (!$request = $observer->getEvent()->getRequest()) {
            return $this;
        }

        $data = $request->getPost('customer');

        if (!isset($data['mpmembership_to'])) {
            return $this;
        }

        $currentExp = date('Y/m/d', $customer->getDuration() + time());
        $expiration = $data['mpmembership_to'];
        $startDate = $customer->getStartDate();

        if ($startDate && strtotime($currentExp) !== strtotime($expiration)) {
            $data['duration'] = $expiration ? strtotime($expiration) - strtotime($startDate) : 0;
        } elseif ($customer->getDuration() === null) {
            $data['start_date'] = $this->date->date();
            $data['duration'] = $expiration ? strtotime($expiration) - time() : 0;
            $data['status'] = CustomerStatus::ACTIVE;
            $data['membership_price'] = 0;
        }

        $customer->saveCustomData($customerId, $data);

        return $this;
    }
}
