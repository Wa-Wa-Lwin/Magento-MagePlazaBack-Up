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
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Data\Customer;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\CustomerFactory;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;

/**
 * Class CustomerPrepareSave
 * @package Mageplaza\Membership\Observer
 */
class CustomerPrepareSave implements ObserverInterface
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var MembershipFactory
     */
    protected $membershipFactory;

    /**
     * @var MembershipResource
     */
    protected $membershipResource;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * CustomerPrepareSave constructor.
     *
     * @param CustomerFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param MembershipFactory $membershipFactory
     * @param MembershipResource $membershipResource
     * @param DateTime $date
     */
    public function __construct(
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        MembershipFactory $membershipFactory,
        MembershipResource $membershipResource,
        DateTime $date
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->membershipFactory = $membershipFactory;
        $this->membershipResource = $membershipResource;
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
        /** @var Http $request */
        $request = $observer->getEvent()->getRequest();

        if (!$request) {
            return $this;
        }

        /** @var Customer $newCustomer */
        $newCustomer = $observer->getEvent()->getCustomer();
        $customerId = $newCustomer->getId();

        $newMembership = $this->membershipFactory->create();
        $this->membershipResource->load($newMembership, $newCustomer->getGroupId());

        if (!$customerId || !$newMembership->getData()) {
            return $this;
        }

        $currentCustomer = $this->customerRepository->getById($customerId);

        $data = $request->getPost('customer');

        if ($currentCustomer->getGroupId() !== $newCustomer->getGroupId()) {
            $newDuration = (float)$newMembership->getDefaultDurationNo() . $newMembership->getDefaultDurationUnit();

            $data['start_date'] = $this->date->date();
            $data['duration'] = strtotime($newDuration) - time();
            $data['status'] = CustomerStatus::ACTIVE;

            $data['membership_price'] = 0;

            if (isset($data['mpmembership_to'])) {
                unset($data['mpmembership_to']);
            }

            $this->customerFactory->create()->saveCustomData($customerId, $data);
        }

        $request->setPostValue('customer', $data);

        return $this;
    }
}
