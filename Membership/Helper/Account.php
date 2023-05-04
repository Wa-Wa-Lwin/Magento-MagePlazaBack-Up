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

namespace Mageplaza\Membership\Helper;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Model\Quote\Item;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\CustomerFactory;
use Mageplaza\Membership\Model\HistoryFactory;
use Mageplaza\Membership\Model\ResourceModel\Customer as MembershipCustomerResource;
use Mageplaza\Membership\Model\ResourceModel\History as HistoryResource;

/**
 * Class Account
 * @package Mageplaza\Membership\Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Account extends AbstractData
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerResource
     */
    protected $customerResource;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var HistoryResource
     */
    protected $historyResource;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var MembershipCustomerResource
     */
    protected $membershipCustomerResource;

    /**
     * Account constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $customerSession
     * @param CustomerRegistry $customerRegistry
     * @param CustomerFactory $customerFactory
     * @param CustomerResource $customerResource
     * @param HistoryFactory $historyFactory
     * @param HistoryResource $historyResource
     * @param HttpContext $httpContext
     * @param DateTime $date
     * @param MembershipCustomerResource $membershipCustomerResource
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        CustomerRegistry $customerRegistry,
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        HistoryFactory $historyFactory,
        HistoryResource $historyResource,
        HttpContext $httpContext,
        DateTime $date,
        MembershipCustomerResource $membershipCustomerResource
    ) {
        $this->customerSession = $customerSession;
        $this->customerRegistry = $customerRegistry;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->historyFactory = $historyFactory;
        $this->historyResource = $historyResource;
        $this->httpContext = $httpContext;
        $this->date = $date;
        $this->membershipCustomerResource = $membershipCustomerResource;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return \Mageplaza\Membership\Model\Customer|Customer|null
     */
    public function getCurrentCustomer()
    {
        return $this->getCustomerById($this->getCustomerSession()->getId());
    }

    /**
     * @return CustomerSession
     */
    public function getCustomerSession()
    {
        return $this->customerSession;
    }

    /**
     * @param int|null $customerId
     *
     * @return \Mageplaza\Membership\Model\Customer|Customer|null
     */
    public function getCustomerById($customerId)
    {
        try {
            if (!$customerId) {
                $customerId = $this->getCustomerSession()->getId();
            }

            return $this->customerRegistry->retrieve($customerId);
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());

            return null;
        }
    }

    /**
     * @param string $email
     *
     * @return Customer|null
     */
    public function getCustomerByEmail($email)
    {
        try {
            return $this->customerRegistry->retrieveByEmail($email);
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());

            return null;
        }
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->_request->isAjax()
            ? $this->getCustomerSession()->isLoggedIn()
            : (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @param Order $order
     * @param Order\Item $item
     *
     * @throws Exception
     */
    public function buyNewMembership(Order $order, Order\Item $item)
    {
        $customerId = $order->getCustomerId();
        $membership = $this->getOptionValue(FieldRenderer::MEMBERSHIP, $item);
        $duration = $this->getOptionValue(FieldRenderer::DURATION, $item);
        $price = $this->getOptionValue(FieldRenderer::PRICE, $item);

        if (!$membership || !$duration) {
            return;
        }

        $now = $this->date->date();

        if ($customer = $this->getCustomerById($customerId)) {
            $customerData = [
                'group_id' => $membership,
                'customer_id' => $customerId,
                'last_membership_id' => $membership,
                'status' => CustomerStatus::ACTIVE,
                'start_date' => $now,
                'duration' => $this->processDuration($duration),
                'membership_price' => $price,
                'old_membership_id' => $customer->getGroupId()
            ];

            $membership = $this->customerFactory->create();
            $this->membershipCustomerResource->load($membership, $customerId);

            if ($membership->getId()) {
                $customerData['old_start_date'] = $membership->getStartDate();
                $customerData['old_duration'] = $membership->getDuration();
            }

            $customer->addData($customerData);
            $this->customerResource->save($customer);
            $membership->saveCustomData($customerId, $customerData);
        }

        $historyData = ['membership_start' => $now];

        $history = $this->historyFactory->create();
        $this->historyResource->load($history, $item->getId(), 'item_id');
        $history->addData($historyData);
        $this->historyResource->save($history);
    }

    /**
     * @param string $data
     *
     * @return int
     */
    public function processDuration($data)
    {
        $data = self::jsonDecode($data);
        if (isset($data['permanent']) || (isset($data['number']) && empty((float)$data['number']))) {
            return 0;
        }

        return strtotime((float)$data['number'] . $data['unit']) - time();
    }

    /**
     * @param string $code
     * @param Item|Item\AbstractItem|Order\Item $item
     *
     * @return array|bool
     */
    public function getOptionValue($code, $item)
    {
        if ($item instanceof Item\AbstractItem && $option = $item->getOptionByCode($code)) {
            return $option->getValue();
        }

        if ($option = $item->getProductOptionByCode($code)) {
            return $option;
        }

        return false;
    }
}
