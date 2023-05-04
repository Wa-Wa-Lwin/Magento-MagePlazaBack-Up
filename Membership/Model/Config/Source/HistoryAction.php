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
 * Class HistoryAction
 * @package Mageplaza\Membership\Model\Config\Source
 */
class HistoryAction implements ArrayInterface
{
    const BUY_NEW = 1;
    //    const EXPIRED = 2;
    const UPGRADE = 3;
    const RE_NEW = 4;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            '' => __(' '),
            self::BUY_NEW => __('Buy New'),
            self::UPGRADE => __('Upgrade'),
            self::RE_NEW => __('Re-new'),
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
