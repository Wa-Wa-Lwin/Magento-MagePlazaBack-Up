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

namespace Mageplaza\Membership\Api\Data;

/**
 * Interface HistoryInterface
 * @package Mageplaza\Membership\Api\Data
 */
interface HistoryInterface
{
    const HISTORY_ID        = 'history_id';
    const ITEM_ID           = 'item_id';
    const CUSTOMER_ID       = 'customer_id';
    const MEMBERSHIP_ID     = 'membership_id';
    const MEMBERSHIP_NAME   = 'membership_name';
    const ITEM_PRODUCT_NAME = 'item_product_name';
    const ACTION            = 'action';
    const AMOUNT            = 'amount';
    const CREATED_DATE      = 'created_date';
    const MEMBERSHIP_START  = 'membership_start';
    const MEMBERSHIP_DATA   = 'membership_data';

    /**
     * @return int
     */
    public function getHistoryId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setHistoryId($value);

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setItemId($value);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCustomerId($value);

    /**
     * @return int
     */
    public function getMembershipId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setMembershipId($value);

    /**
     * @return string
     */
    public function getMembershipName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMembershipName($value);

    /**
     * @return string
     */
    public function getItemProductName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setItemProductName($value);

    /**
     * @return int
     */
    public function getAction();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setAction($value);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setAmount($value);

    /**
     * @return string
     */
    public function getCreatedDate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedDate($value);

    /**
     * @return string
     */
    public function getMembershipStart();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMembershipStart($value);

    /**
     * @return string
     */
    public function getMembershipData();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMembershipData($value);
}
