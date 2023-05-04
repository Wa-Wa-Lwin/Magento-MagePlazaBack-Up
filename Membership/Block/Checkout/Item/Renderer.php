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

namespace Mageplaza\Membership\Block\Checkout\Item;

use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Checkout\Model\Session;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Membership\Helper\Data;

/**
 * Class Renderer
 * @package Mageplaza\Membership\Block\Checkout\Item
 */
class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Renderer constructor.
     *
     * @param Context $context
     * @param Configuration $productConfig
     * @param Session $checkoutSession
     * @param ImageBuilder $imageBuilder
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param ManagerInterface $messageManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param Manager $moduleManager
     * @param InterpretationStrategyInterface $messageInterpretationStrategy
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Configuration $productConfig,
        Session $checkoutSession,
        ImageBuilder $imageBuilder,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        ManagerInterface $messageManager,
        PriceCurrencyInterface $priceCurrency,
        Manager $moduleManager,
        InterpretationStrategyInterface $messageInterpretationStrategy,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct(
            $context,
            $productConfig,
            $checkoutSession,
            $imageBuilder,
            $urlHelper,
            $messageManager,
            $priceCurrency,
            $moduleManager,
            $messageInterpretationStrategy,
            $data
        );
    }

    /**
     * Return membership custom options
     *
     * @return array
     */
    public function getOptionList()
    {
        $item = $this->getItem();

        $options = $this->_productConfig->getCustomOptions($item);

        return $this->helper->getOptionList($item, $options);
    }
}
