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
 * Interface CustomerInterface
 * @package Mageplaza\Membership\Api\Data
 */
interface CustomerInterface
{
    const CUSTOMER_ID            = 'customer_id';
    const LAST_MEMBERSHIP_ID     = 'last_membership_id';
    const INACTIVE_MEMBERSHIP_ID = 'inactive_membership_id';
    const STATUS                 = 'status';
    const START_DATE             = 'start_date';
    const DURATION               = 'duration';
    const MEMBERSHIP_PRICE       = 'membership_price';

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
    public function getLastMembershipId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setLastMembershipId($value);

    /**
     * @return int
     */
    public function getInactiveMembershipId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setInactiveMembershipId($value);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getStartDate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStartDate($value);

    /**
     * @return string
     */
    public function getDuration();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDuration($value);

    /**
     * @return float
     */
    public function getMembershipPrice();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setMembershipPrice($value);
}
