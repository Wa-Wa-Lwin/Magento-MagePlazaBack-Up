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

use Magento\Framework\Model\AbstractModel;
use Mageplaza\Membership\Api\Data\HistoryInterface;

/**
 * Class History
 * @package Mageplaza\Membership\Model
 */
class History extends AbstractModel implements HistoryInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel\History::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getHistoryId()
    {
        return $this->getData(self::HISTORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setHistoryId($value)
    {
        return $this->setData(self::HISTORY_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemId($value)
    {
        return $this->setData(self::ITEM_ID, $value);
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
    public function getMembershipId()
    {
        return $this->getData(self::MEMBERSHIP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setMembershipId($value)
    {
        return $this->setData(self::MEMBERSHIP_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMembershipName()
    {
        return $this->getData(self::MEMBERSHIP_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setMembershipName($value)
    {
        return $this->setData(self::MEMBERSHIP_NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemProductName()
    {
        return $this->getData(self::ITEM_PRODUCT_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemProductName($value)
    {
        return $this->setData(self::ITEM_PRODUCT_NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($value)
    {
        return $this->setData(self::ACTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($value)
    {
        return $this->setData(self::AMOUNT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedDate()
    {
        return $this->getData(self::CREATED_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedDate($value)
    {
        return $this->setData(self::CREATED_DATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMembershipStart()
    {
        return $this->getData(self::MEMBERSHIP_START);
    }

    /**
     * {@inheritdoc}
     */
    public function setMembershipStart($value)
    {
        return $this->setData(self::MEMBERSHIP_START, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMembershipData()
    {
        return $this->getData(self::MEMBERSHIP_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setMembershipData($value)
    {
        return $this->setData(self::MEMBERSHIP_DATA, $value);
    }
}
