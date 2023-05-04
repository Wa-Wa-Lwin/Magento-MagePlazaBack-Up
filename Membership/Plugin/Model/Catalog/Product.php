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

namespace Mageplaza\Membership\Plugin\Model\Catalog;

use Closure;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\Product\Type\Membership;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;

/**
 * Class Product
 * @package Mageplaza\Membership\Plugin\Model\Catalog
 */
class Product
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
     * Product constructor.
     *
     * @param MembershipFactory $membershipFactory
     * @param MembershipResource $membershipResource
     */
    public function __construct(
        MembershipFactory $membershipFactory,
        MembershipResource $membershipResource
    ) {
        $this->membershipFactory = $membershipFactory;
        $this->membershipResource = $membershipResource;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product $subject
     * @param Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws LocalizedException
     */
    public function aroundSave(\Magento\Catalog\Model\ResourceModel\Product $subject, Closure $proceed, $product)
    {
        if ($product->getTypeId() !== Membership::TYPE_MEMBERSHIP) {
            return $proceed($product);
        }

        $orgMembership = $product->getOrigData(FieldRenderer::MEMBERSHIP);
        $newMembership = $product->getData(FieldRenderer::MEMBERSHIP);

        $membership = $this->membershipFactory->create();
        $this->membershipResource->load($membership, $orgMembership);
        $defaultProduct = $membership->getDefaultProduct();

        if ($orgMembership !== $newMembership && $defaultProduct && $defaultProduct === $product->getId()) {
            throw new LocalizedException(
                __('Cannot change membership of this product since it is used for default product option.')
            );
        }

        return $proceed($product);
    }
}
