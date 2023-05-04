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

namespace Mageplaza\Membership\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Mageplaza\Membership\Helper\Data;

/**
 * Class LayoutProcessor
 * @package Mageplaza\Membership\Block\Checkout
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * LayoutProcessor constructor.
     *
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function process($jsLayout)
    {
        if (!$this->data->isEnabled()) {
            return $jsLayout;
        }

        $fields = &$jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']
        ['cart_items']['children'];

        $membership = [
            'component' => 'Mageplaza_Membership/js/view/summary/item/details/membership',
            'template' => 'Mageplaza_Membership/summary/item/details/membership',
            'displayArea' => 'after_details'
        ];

        if ($this->data->isOscPage()) {
            $fields = &$jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']
            ['cart_items']['children']['details']['children'];

            $membership['template'] = 'Mageplaza_Membership/summary/item/details/osc-membership';
            $membership['displayArea'] = 'after_item_details';
        }

        $fields['membership'] = $membership;

        return $jsLayout;
    }
}
