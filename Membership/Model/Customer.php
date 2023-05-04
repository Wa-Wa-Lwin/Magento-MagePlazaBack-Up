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

namespace Mageplaza\Membership\Model;

use Exception;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mageplaza\Membership\Api\Data\CustomerInterface;

/**
 * Class Customer
 * @package Mageplaza\Membership\Model
 * @method AbstractDb|ResourceModel\Customer getResource()
 * @method getOldMembershipId()
 * @method getOldStartDate()
 * @method getOldDuration()
 */
class Customer extends AbstractModel implements CustomerInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Customer::class);
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     */
    public function attachCustomData(AbstractModel $object)
    {
        $object->addData($this->getData());

        return $this;
    }

    /**
     * @param int $objId
     * @param array $data
     *
     * @return $this
     * @throws Exception
     */
    public function saveCustomData($objId, $data)
    {
        $this->addData($data)->setId($objId)->getResource()->save($this);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($value)
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastMembershipId()
    {
        return $this->getData(self::LAST_MEMBERSHIP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastMembershipId($value)
    {
        return $this->setData(self::LAST_MEMBERSHIP_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getInactiveMembershipId()
    {
        return $this->getData(self::INACTIVE_MEMBERSHIP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setInactiveMembershipId($value)
    {
        return $this->setData(self::INACTIVE_MEMBERSHIP_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStartDate()
    {
        return $this->getData(self::START_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartDate($value)
    {
        return $this->setData(self::START_DATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDuration()
    {
        return $this->getData(self::DURATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDuration($value)
    {
        return $this->setData(self::DURATION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMembershipPrice()
    {
        return $this->getData(self::MEMBERSHIP_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMembershipPrice($value)
    {
        return $this->setData(self::MEMBERSHIP_PRICE, $value);
    }
}
