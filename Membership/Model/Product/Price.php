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

namespace Mageplaza\Membership\Model\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\Price as CatalogPrice;
use Magento\Quote\Model\Quote\Item\Option;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;

/**
 * Class Price
 * @package Mageplaza\Membership\Model\Product
 */
class Price extends CatalogPrice
{
    /**
     * @param Product $product
     *
     * @return float
     */
    public function getPrice($product)
    {
        /** @var Option $option */
        $option = $product->getCustomOption(FieldRenderer::PRICE);

        return $option ? $option->getValue() : 0;
    }

    /**
     * @param float|null $qty
     * @param Product $product
     *
     * @return float
     */
    public function getFinalPrice($qty, $product)
    {
        if ($qty === null && $product->getCalculatedFinalPrice() !== null) {
            return $product->getCalculatedFinalPrice();
        }

        /** @var Option $option */
        $option = $product->getCustomOption(FieldRenderer::PRICE);

        $finalPrice = $option ? $option->getValue() : 0;

        $product->setFinalPrice($finalPrice);

        $this->_eventManager->dispatch('catalog_product_get_final_price', ['product' => $product, 'qty' => $qty]);

        $finalPrice = $product->getData('final_price');
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $finalPrice = max(0, $finalPrice);

        $product->setFinalPrice($finalPrice);

        return $finalPrice;
    }
}
