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

namespace Mageplaza\Membership\Plugin\Helper\Catalog;

use Closure;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\Product\Type\Membership;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;

/**
 * Class Output
 * @package Mageplaza\Membership\Plugin\Helper\Catalog
 */
class Output
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
     * @param \Magento\Catalog\Helper\Output $subject
     * @param Closure $proceed
     * @param Product $product
     *
     * @param string $attributeHtml
     * @param string $attributeName
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws LocalizedException
     */
    public function aroundProductAttribute(
        \Magento\Catalog\Helper\Output $subject,
        Closure $proceed,
        Product $product,
        $attributeHtml,
        $attributeName
    ) {
        $result = $proceed($product, $attributeHtml, $attributeName);

        if ($attributeName !== 'description' || $product->getTypeId() !== Membership::TYPE_MEMBERSHIP) {
            return $result;
        }

        $membership = $this->membershipFactory->create();
        $this->membershipResource->load($membership, $product->getData(FieldRenderer::MEMBERSHIP));
        $benefit = $this->getBenefit($membership);

        if (empty($benefit)) {
            return $result;
        }

        $result .= '<div class="mpmembership-product-benefit"><p><strong>' . __('Benefits') . '</strong></p><ul>';
        foreach ($benefit as $item) {
            $result .= '<li>' . $item . '</li>';
        }
        $result .= '</ul></div>';

        return $result;
    }

    /**
     * @param \Mageplaza\Membership\Model\Membership $membership
     *
     * @return array
     * @throws LocalizedException
     */
    public function getBenefit($membership)
    {
        $storeId = $this->helper->getScopeId();
        $value = Data::jsonDecode($membership->getBenefit());

        if (empty($value['option']['value'])) {
            return [];
        }

        $result = [];

        foreach ($value['option']['value'] as $index => $item) {
            $result[$index] = $item[0];

            if (!empty($item[$storeId])) {
                $result[$index] = $item[$storeId];
            }
        }

        return $result;
    }
}
