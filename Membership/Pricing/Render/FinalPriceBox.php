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

namespace Mageplaza\Membership\Pricing\Render;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Render\FinalPriceBox as CatalogRender;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\DurationType;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;

/**
 * Class FinalPriceBox
 * @package Mageplaza\Membership\Pricing\Render
 */
class FinalPriceBox extends CatalogRender
{
    /**
     * @var float
     */
    protected $_price = 0;

    /**
     * @var AmountFactory
     */
    protected $amountFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->findPriceValue();
    }

    /**
     * @return $this
     */
    protected function findPriceValue()
    {
        /** @var Product $product */
        $product = $this->saleableItem;

        $duration = $product->getData(FieldRenderer::DURATION);
        $price = $product->getData(FieldRenderer::PRICE);

        if ($duration === DurationType::CUSTOM) {
            $options = $product->getData(FieldRenderer::OPTIONS);

            if (is_string($options)) {
                $options = Data::jsonDecode($options);
            }

            if (!empty($options)) {
                $price = $options[0]['price'];
            }
        }

        $this->_price = $price;

        return $this;
    }

    /**
     * @return AmountFactory
     */
    public function getAmountFactory()
    {
        if ($this->amountFactory === null) {
            $this->amountFactory = ObjectManager::getInstance()->get(AmountFactory::class);
        }

        return $this->amountFactory;
    }

    /**
     * @return PriceCurrencyInterface
     */
    protected function getPriceCurrency()
    {
        if ($this->priceCurrency === null) {
            $this->priceCurrency = ObjectManager::getInstance()->get(PriceCurrencyInterface::class);
        }

        return $this->priceCurrency;
    }

    /**
     * @return AmountInterface
     */
    public function getMembershipPrice()
    {
        $minPrice = $this->getPriceCurrency()->convert($this->_price);

        return $this->getAmountFactory()->create($minPrice);
    }

    /**
     * @return bool
     */
    public function isFixedPrice()
    {
        return $this->saleableItem->getData(FieldRenderer::DURATION) !== DurationType::CUSTOM;
    }
}
