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

namespace Mageplaza\Membership\Plugin\Ui\Customer;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\Event\ManagerInterface;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;

/**
 * Class DataProvider
 * @package Mageplaza\Membership\Plugin\Ui\Customer
 */
class DataProvider
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
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * CustomerLoadAfter constructor.
     *
     * @param CustomerFactory $customerFactory
     * @param CustomerResource $customerResource
     * @param ManagerInterface $eventManager
     * @param Data $helper
     */
    public function __construct(
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        ManagerInterface $eventManager,
        Data $helper
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->eventManager = $eventManager;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Customer\Ui\Component\DataProvider $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws Exception
     */
    public function afterGetData(\Magento\Customer\Ui\Component\DataProvider $subject, $result)
    {
        if (isset($result['items'])) {
            foreach ($result['items'] as &$item) {
                /** @var Customer|\Mageplaza\Membership\Model\Customer $customer */
                $customer = $this->customerFactory->create();

                $this->customerResource->load($customer, $item['entity_id']);

                $this->eventManager->dispatch('mpmembership_customer_load_after', ['customer' => $customer]);

                $start = $customer->getStartDate();
                $duration = (int)$customer->getDuration();

                if (!$start || !$duration || (int)$customer->getStatus() !== CustomerStatus::ACTIVE) {
                    continue;
                }

                if (strtotime($start) + $duration <= time()) {
                    $item['group_id'][0] = $this->helper->getDefaultGroup();
                }
            }
        }

        return $result;
    }
}
