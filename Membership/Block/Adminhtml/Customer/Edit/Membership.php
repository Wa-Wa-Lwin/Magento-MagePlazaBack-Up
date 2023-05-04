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

namespace Mageplaza\Membership\Block\Adminhtml\Customer\Edit;

use IntlDateFormatter;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\MembershipStatus;
use Mageplaza\Membership\Model\CustomerFactory;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Customer as CustomerResource;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;

/**
 * Class Membership
 * @package Mageplaza\Membership\Block\Adminhtml\Customer\Edit
 */
class Membership extends Template
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
     * @var MembershipFactory
     */
    protected $membershipFactory;

    /**
     * @var MembershipResource
     */
    protected $membershipResource;

    /**
     * Membership constructor.
     *
     * @param Context $context
     * @param CustomerFactory $customerFactory
     * @param CustomerResource $customerResource
     * @param MembershipFactory $membershipFactory
     * @param MembershipResource $membershipResource
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        MembershipFactory $membershipFactory,
        MembershipResource $membershipResource,
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->membershipFactory = $membershipFactory;
        $this->membershipResource = $membershipResource;

        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getCustomerData()
    {
        $data = $this->_backendSession->getCustomerData()['account'];

        $customer = $this->customerFactory->create();
        $this->customerResource->load($customer, $data['id']);
        $membership = $this->membershipFactory->create();
        $this->membershipResource->load($membership, $data['group_id']);

        if ((int)$membership->getStatus() !== MembershipStatus::ACTIVE) {
            return [];
        }

        $name = $membership->getData('customer_group_code');
        $label = Data::jsonDecode($membership->getName());
        if (!empty($label[0])) {
            $name = $label[0];
        }

        $duration = $customer->getDuration();

        $timestamp = strtotime($customer->getStartDate()) + $duration;

        return [
            'name' => $name,
            'end' => $duration
                ? $this->formatTime(date('Y/m/d h:i:s', $timestamp), IntlDateFormatter::MEDIUM, true)
                : __('Permanent')
        ];
    }
}
