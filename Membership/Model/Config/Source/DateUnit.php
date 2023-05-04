<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

use Magento\Framework\Option\ArrayInterface;

/**
 * Class DateUnit
 * @package Mageplaza\Membership\Model\Config\Source
 */
class DateUnit implements ArrayInterface
{
    const DAY = 'day';
    const MONTH = 'month';
    const YEAR = 'year';

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::DAY => __('Day(s)'),
            self::MONTH => __('Month(s)'),
            self::YEAR => __('Year(s)')
        ];
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function getUnitStatic($value)
    {
        $dateUnits = self::getOptionArray();

        return isset($dateUnits[$value]) ? $dateUnits[$value] : '';
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function getUnitByValue($value)
    {
        return self::getUnitStatic($value);
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
