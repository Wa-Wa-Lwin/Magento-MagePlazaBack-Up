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
use Magento\Customer\Model\Group;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;

/**
 * Class CustomerGroupDeleteAfter
 * @package Mageplaza\Membership\Observer
 */
class CustomerGroupDeleteAfter implements ObserverInterface
{
    /**
     * @var MembershipFactory
     */
    protected $membershipFactory;

    /**
     * @var MembershipResource
     */
    protected $membershipResource;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * CustomerGroupDeleteAfter constructor.
     *
     * @param MembershipFactory $membershipFactory
     * @param MembershipResource $membershipResource
     * @param Data $helper
     */
    public function __construct(
        MembershipFactory $membershipFactory,
        MembershipResource $membershipResource,
        Data $helper
    ) {
        $this->membershipFactory = $membershipFactory;
        $this->membershipResource = $membershipResource;
        $this->helper = $helper;
    }

    /**
     * After load observer for customer group
     *
     * @param Observer $observer
     *
     * @return $this
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Group $object */
        $object = $observer->getEvent()->getObject();

        if ($object instanceof AbstractModel) {
            $membership = $this->membershipFactory->create();
            $this->membershipResource->load($membership, $object->getId());
            $this->membershipResource->delete($membership);
        }

        return $this;
    }
}
