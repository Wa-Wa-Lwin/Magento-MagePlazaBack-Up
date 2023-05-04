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

namespace Mageplaza\Membership\Model\Config\Source;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class FieldRenderer
 * @package Mageplaza\Membership\Model\Config\Source
 */
class FieldRenderer extends AbstractModel implements ArrayInterface
{
    const MEMBERSHIP = 'mpmembership';
    const DURATION = 'mpmembership_duration';
    const PRICE = 'mpmembership_price_fixed';
    const OPTIONS = 'mpmembership_duration_options';
    const ACTION = 'mpmembership_action';

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::MEMBERSHIP => __('Membership'),
            self::DURATION => __('Duration'),
            self::PRICE => __('Price'),
            self::OPTIONS => __('Duration Options'),
            self::ACTION => __('Action'),
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
