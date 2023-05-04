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

namespace Mageplaza\Membership\Model\ResourceModel\Membership\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * Class Collection
 * @package Mageplaza\Membership\Model\ResourceModel\Membership\Grid
 */
class Collection extends SearchResult
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'membership_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_membership_list_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'membership_list_collection';

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinRight(
            ['customer_group' => $this->getTable('customer_group')],
            'main_table.membership_id = customer_group.customer_group_id',
            [
                'membership_id' => 'customer_group.customer_group_id',
                'customer_group_id' => 'customer_group.customer_group_id',
                'customer_group_code' => 'customer_group.customer_group_code'
            ]
        )->where('customer_group_id > 0');

        return $this;
    }
}
