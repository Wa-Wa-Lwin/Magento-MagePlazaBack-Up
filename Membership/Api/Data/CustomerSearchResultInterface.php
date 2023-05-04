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
 * Interface CustomerSearchResultInterface
 * @package Mageplaza\Membership\Api\Data
 */
interface CustomerSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Mageplaza\Membership\Api\Data\CustomerInterface[]
     */
    public function getItems();

    /**
     * @param \Mageplaza\Membership\Api\Data\CustomerInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
