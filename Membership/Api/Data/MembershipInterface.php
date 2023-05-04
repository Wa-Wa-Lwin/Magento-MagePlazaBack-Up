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
 * Interface MembershipInterface
 * @package Mageplaza\Membership\Api\Data
 */
interface MembershipInterface
{
    const MEMBERSHIP_ID         = 'membership_id';
    const NAME                  = 'name';
    const STATUS                = 'status';
    const LEVEL                 = 'level';
    const CUSTOMER_GROUP        = 'customer_group';
    const IS_FEATURED           = 'is_featured';
    const FEATURED_IMAGE        = 'featured_image';
    const FEATURED_LABEL        = 'featured_label';
    const SORT_ORDER            = 'sort_order';
    const DEFAULT_DURATION_UNIT = 'default_duration_unit';
    const DEFAULT_DURATION_NO   = 'default_duration_no';
    const IMAGE                 = 'image';
    const BACKGROUND_COLOR      = 'background_color';
    const DEFAULT_PRODUCT       = 'default_product';
    const BENEFIT               = 'benefit';
    const CREATED_AT            = 'created_at';
    const UPDATED_AT            = 'updated_at';

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
     * @return string|null
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);

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
     * @return int
     */
    public function getLevel();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setLevel($value);

    /**
     * @return string
     */
    public function getCustomerGroup();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCustomerGroup($value);

    /**
     * @return int
     */
    public function getIsFeatured();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setIsFeatured($value);

    /**
     * @return string
     */
    public function getFeaturedImage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFeaturedImage($value);

    /**
     * @return string
     */
    public function getFeaturedLabel();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFeaturedLabel($value);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setSortOrder($value);

    /**
     * @return string
     */
    public function getDefaultDurationUnit();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDefaultDurationUnit($value);

    /**
     * @return float
     */
    public function getDefaultDurationNo();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setDefaultDurationNo($value);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setImage($value);

    /**
     * @return string
     */
    public function getBackgroundColor();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setBackgroundColor($value);

    /**
     * @return string
     */
    public function getDefaultProduct();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDefaultProduct($value);

    /**
     * @return string|null
     */
    public function getBenefit();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setBenefit($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUpdatedAt($value);
}
