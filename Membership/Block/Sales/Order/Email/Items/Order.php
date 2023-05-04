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

namespace Mageplaza\Membership\Block\Sales\Order\Email\Items;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder;
use Mageplaza\Membership\Helper\Data;

/**
 * Class Order
 * @package Mageplaza\Membership\Block\Order\Email\Items
 */
class Order extends DefaultOrder
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Order constructor.
     *
     * @param Template\Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getItemOptions()
    {
        $item = $this->getItem();

        return $this->helper->getOptionList($item, parent::getItemOptions());
    }
}
