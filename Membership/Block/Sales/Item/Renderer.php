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

namespace Mageplaza\Membership\Block\Sales\Item;

use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer;
use Magento\Sales\Model\Order\Item;
use Mageplaza\Membership\Helper\Data;

/**
 * Class Renderer
 * @package Mageplaza\Membership\Block\Sales\Item
 */
class Renderer extends DefaultRenderer
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Renderer constructor.
     *
     * @param Context $context
     * @param StringUtils $string
     * @param OptionFactory $productOptionFactory
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        StringUtils $string,
        OptionFactory $productOptionFactory,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $string, $productOptionFactory, $data);
    }

    /**
     * Return store credit custom options
     *
     * @return array
     */
    public function getItemOptions()
    {
        $item = $this->getItem();

        if (!($item instanceof Item)) {
            $item = $item->getOrderItem();
        }

        return $this->helper->getOptionList($item, parent::getItemOptions());
    }
}
