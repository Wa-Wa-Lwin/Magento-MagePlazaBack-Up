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

namespace Mageplaza\Membership\Plugin\Model\Wishlist;

use Closure;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\Product\Type\Membership;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;

/**
 * Class Output
 * @package Mageplaza\Membership\Plugin\Helper\Catalog
 */
class Item
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var MembershipFactory
     */
    protected $membershipFactory;

    /**
     * @var MembershipResource
     */
    protected $membershipResource;

    /**
     * Product constructor.
     *
     * @param Data $helper
     * @param MembershipFactory $membershipFactory
     * @param MembershipResource $membershipResource
     */
    public function __construct(
        Data $helper,
        MembershipFactory $membershipFactory,
        MembershipResource $membershipResource
    ) {
        $this->helper = $helper;
        $this->membershipFactory = $membershipFactory;
        $this->membershipResource = $membershipResource;
    }

    /**
     * @param \Magento\Wishlist\Model\Item $subject
     * @param Closure $proceed
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundCanHaveQty(\Magento\Wishlist\Model\Item $subject, Closure $proceed)
    {
        $product = $subject->getProduct();

        if ($product->getTypeId() === Membership::TYPE_MEMBERSHIP) {
            return false;
        }

        return $proceed;
    }
}
