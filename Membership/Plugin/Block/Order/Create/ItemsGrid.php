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

namespace Mageplaza\Membership\Plugin\Block\Order\Create;

use Closure;
use Magento\Quote\Model\Quote\Item;
use Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\Product\Type\Membership;

/**
 * Class ItemsGrid
 * @package Mageplaza\Membership\Plugin\Block\Order\Create
 */
class ItemsGrid
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * ItemsGrid constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param Grid $subject
     * @param Closure $proceed
     * @param Item $item
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetConfigureButtonHtml(Grid $subject, Closure $proceed, Item $item)
    {
        $result = $proceed($item);
        $product = $item->getProduct();
        $option = $this->helper->getOptionValue(FieldRenderer::DURATION, $item);

        if (!$option || $product->getTypeId() !== Membership::TYPE_MEMBERSHIP) {
            return $result;
        }

        $duration = $this->helper->getDurationText($option);

        return '<p>' . __('Duration: ') . $duration . '</p>' . $result;
    }
}
