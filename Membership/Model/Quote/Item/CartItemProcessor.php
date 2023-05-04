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

namespace Mageplaza\Membership\Model\Quote\Item;

use Magento\Framework\DataObject\Factory;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\CartItemProcessorInterface;
use Mageplaza\Membership\Helper\Data;

/**
 * Class CartItemProcessor
 * @package Mageplaza\Membership\Model\Quote\Item
 */
class CartItemProcessor implements CartItemProcessorInterface
{
    /**
     * @var Factory
     */
    protected $objectFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * CartItemProcessor constructor.
     *
     * @param Factory $objectFactory
     * @param Data $helperData
     */
    public function __construct(
        Factory $objectFactory,
        Data $helperData
    ) {
        $this->objectFactory = $objectFactory;
        $this->helperData = $helperData;
    }

    /**
     * @inheritdoc
     */
    public function convertToBuyRequest(CartItemInterface $cartItem)
    {
        if ($cartItem->getProductOption() &&
            $cartItem->getProductOption()->getExtensionAttributes() &&
            ($cartItem->getProductOption()->getExtensionAttributes()->getMpmembershipData() ||
                $cartItem->getProductOption()->getExtensionAttributes()->getMpmembershipData() === '0'
            )
        ) {
            return $this->objectFactory->create(
                [
                    'mpmembership_data' => $cartItem->getProductOption()
                        ->getExtensionAttributes()->getMpmembershipData(),
                    'quote_id' => $cartItem->getQuoteId()
                ]
            );
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function processOptions(CartItemInterface $cartItem)
    {
        return $cartItem;
    }
}
