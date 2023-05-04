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

namespace Mageplaza\Membership\Plugin\Block\Product;

use Closure;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Membership\Block\Product\Listing;
use Mageplaza\Membership\Model\Product\Type\Membership;

/**
 * Class ListProduct
 * @package Mageplaza\Membership\Plugin\Block\Product
 */
class ListProduct
{
    /**
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param Closure $proceed
     * @param $name
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetBlockHtml(\Magento\Catalog\Block\Product\ListProduct $subject, Closure $proceed, $name)
    {
        $html = $proceed($name);

        if ($name !== 'formkey') {
            return $html;
        }

        $html .= '<input type="hidden" name="mpmembership_data" class="mpmembership-data"/>';
        $html .= '<input type="hidden" name="qty" value="1"/>';

        return $html;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param Closure $proceed
     * @param Product $product
     *
     * @return string
     * @throws LocalizedException
     */
    public function aroundGetProductPrice(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        Closure $proceed,
        Product $product
    ) {
        return $this->getHtml($subject, $product) . $proceed($product);
    }

    /**
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param Product $product
     *
     * @return string
     * @throws LocalizedException
     */
    private function getHtml($subject, $product)
    {
        if ($product->getTypeId() !== Membership::TYPE_MEMBERSHIP) {
            return '';
        }

        /** @var Listing $block */
        $block = $subject->getLayout()->createBlock(Listing::class);
        $block->setProduct($product);

        return $block->toHtml();
    }
}
