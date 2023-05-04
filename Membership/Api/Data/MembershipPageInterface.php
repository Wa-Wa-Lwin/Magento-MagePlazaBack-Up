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
 * Interface MembershipPageInterface
 * @package Mageplaza\Membership\Api\Data
 */
interface MembershipPageInterface extends MembershipInterface
{
    const DURATION         = 'duration';
    const PRICE            = 'price';
    const OPTIONS          = 'options';
    const IS_OUT_OF_STOCK  = 'is_out_of_stock';

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
     * @return string
     */
    public function getPrice();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPrice($value);

    /**
     * @return string
     */
    public function getOptions();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOptions($value);

    /**
     * @return bool
     */
    public function getIsOutOfStock();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsOutOfStock($value);
}
