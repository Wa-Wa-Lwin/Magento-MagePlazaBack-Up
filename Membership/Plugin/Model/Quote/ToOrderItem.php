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

namespace Mageplaza\Membership\Plugin\Model\Quote;

use Closure;
use Magento\Catalog\Model\Product\Configuration\Item\Option;
use Magento\Quote\Model\Quote\Item;
use Magento\Sales\Model\Order\Item as OrderItem;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;

/**
 * Class ToOrderItem
 * @package Mageplaza\Membership\Plugin\Model\Quote
 */
class ToOrderItem
{
    /**
     * @param Item\ToOrderItem $subject
     * @param Closure $proceed
     * @param Item\AbstractItem $item
     * @param $additional
     *
     * @return OrderItem
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundConvert(
        Item\ToOrderItem $subject,
        Closure $proceed,
        Item\AbstractItem $item,
        $additional = []
    ) {
        /** @var OrderItem $orderItem */
        $orderItem = $proceed($item, $additional);

        $productOptions = $orderItem->getProductOptions();

        foreach (array_keys(FieldRenderer::getOptionArray()) as $key) {
            /** @var Option $option */
            if ($option = $item->getProduct()->getCustomOption($key)) {
                $productOptions[$key] = $option->getValue();
            }
        }

        $orderItem->setProductOptions($productOptions);

        return $orderItem;
    }
}
