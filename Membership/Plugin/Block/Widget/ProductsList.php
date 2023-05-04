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

namespace Mageplaza\Membership\Plugin\Block\Widget;

use Closure;
use Magento\Catalog\Model\Product;
use Magento\CatalogWidget\Block\Product\ProductsList as ProductsListPlugin;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Render;
use Mageplaza\Membership\Block\Product\Listing;
use Mageplaza\Membership\Model\Product\Type\Membership;

/**
 * Class ProductsList
 * @package Mageplaza\Membership\Plugin\Block\Widget
 */
class ProductsList
{
    /**
     * @param ProductsListPlugin $subject
     * @param Closure $proceed
     * @param $name
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetBlockHtml(ProductsListPlugin $subject, Closure $proceed, $name)
    {
        $html = $proceed($name);

        if ($name !== 'formkey') {
            return $html;
        }

        $html .= '<input type="hidden" name="mpmembership_data" class="mpmembership-data"/>';

        return $html;
    }

    /**
     * @param ProductsListPlugin $subject
     * @param Closure $proceed
     * @param Product $product
     * @param null $priceType
     * @param string $renderZone
     * @param array $arguments
     *
     * @return string
     * @throws LocalizedException
     */
    public function aroundGetProductPriceHtml(
        ProductsListPlugin $subject,
        Closure $proceed,
        Product $product,
        $priceType = null,
        $renderZone = Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        return $proceed($product, $priceType, $renderZone, $arguments) . $this->getHtml($subject, $product);
    }

    /**
     * @param ProductsListPlugin $subject
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
