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

namespace Mageplaza\Membership\Plugin\Model\Customer;

use Mageplaza\Membership\Model\CustomerFactory;

/**
 * Class DataProvider
 * @package Mageplaza\Membership\Plugin\Model\Customer
 */
class DataProvider
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param CustomerFactory $customerFactory
     */
    public function __construct(CustomerFactory $customerFactory)
    {
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param \Magento\Customer\Model\Customer\DataProvider $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(\Magento\Customer\Model\Customer\DataProvider $subject, $result)
    {
        if (empty($result)) {
            return $result;
        }

        foreach ($result as &$item) {
            if (empty($item['customer']['entity_id'])) {
                continue;
            }

            $customer = &$item['customer'];

            $model = $this->customerFactory->create();
            $model->getResource()->load($model, $customer['entity_id']);
            $duration = $model->getDuration();
            $timestamp = strtotime($model->getStartDate()) + $duration;

            $customer['mpmembership_to'] = $duration ? date('Y/m/d', $timestamp) : null;
        }

        return $result;
    }
}
