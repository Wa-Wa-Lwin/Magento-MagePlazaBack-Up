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

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\ProductOptionExtensionFactory;
use Magento\Quote\Model\Quote\ProductOptionFactory;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\Product\Type\Membership;

/**
 * Class QuoteRepository
 * @package Mageplaza\Membership\Plugin\Model\Quote
 */
class QuoteRepository
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ProductOptionFactory
     */
    private $productOptionFactory;

    /**
     * @var ProductOptionExtensionFactory
     */
    private $extensionFactory;

    /**
     * QuoteRepository constructor.
     *
     * @param ProductOptionFactory $productOptionFactory
     * @param ProductOptionExtensionFactory $extensionFactory
     * @param Data $helper
     */
    public function __construct(
        ProductOptionFactory $productOptionFactory,
        ProductOptionExtensionFactory $extensionFactory,
        Data $helper
    ) {
        $this->productOptionFactory = $productOptionFactory;
        $this->extensionFactory = $extensionFactory;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Quote\Model\QuoteRepository $subject
     * @param CartInterface $quote
     *
     * @return CartInterface
     */
    public function afterGet(\Magento\Quote\Model\QuoteRepository $subject, $quote)
    {
        $this->addDuration($quote);

        return $quote;
    }

    /**
     * @param \Magento\Quote\Model\QuoteRepository $subject
     * @param CartInterface $quote
     *
     * @return CartInterface
     */
    public function afterGetForCustomer(\Magento\Quote\Model\QuoteRepository $subject, $quote)
    {
        $this->addDuration($quote);

        return $quote;
    }

    /**
     * @param CartInterface $quote
     */
    public function addDuration($quote)
    {
        $items = $quote->getItems();
        if ($items) {
            foreach ($items as $item) {
                if ($item->getItemId() && $item->getProductType() === Membership::TYPE_MEMBERSHIP) {
                    $option = $item->getOptionByCode(FieldRenderer::DURATION);
                    if ($option && $option->getValue()) {
                        $productOption = $item->getProductOption() ?: $this->productOptionFactory->create();
                        $extensibleAttribute = $productOption->getExtensionAttributes() ?:
                            $this->extensionFactory->create();

                        $extensibleAttribute->setMpMembershipDuration(
                            $this->helper->getDurationText($option->getValue())
                        );

                        $productOption->setExtensionAttributes($extensibleAttribute);
                        $item->setProductOption($productOption);
                    }
                }
            }
        }
    }
}
