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

namespace Mageplaza\Membership\Block\Sales\Order\Email;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Block\Order\Email\Items\DefaultItems;
use Mageplaza\Membership\Helper\Data;

/**
 * Class Items
 * @package Mageplaza\Membership\Block\Sales\Order\Email
 */
class Items extends DefaultItems
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Items constructor.
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
     * Return store credit custom options
     *
     * @return array
     */
    public function getItemOptions()
    {
        $item = $this->getItem()->getOrderItem();

        return $this->helper->getOptionList($item, parent::getItemOptions());
    }
}
