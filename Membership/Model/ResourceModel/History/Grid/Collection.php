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

namespace Mageplaza\Membership\Model\ResourceModel\History\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * Class Collection
 * @package Mageplaza\Membership\Model\ResourceModel\History\Grid
 */
class Collection extends SearchResult
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'history_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_membership_history_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'membership_history_collection';

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['customer_entity' => $this->getTable('customer_entity')],
            'main_table.customer_id = customer_entity.entity_id'
        )->joinLeft(
            ['mpmembership' => $this->getTable('mageplaza_membership_list')],
            'main_table.membership_id = mpmembership.membership_id'
        )->joinLeft(
            ['customer_group' => $this->getTable('customer_group')],
            'main_table.membership_id = customer_group.customer_group_id'
        );

        return $this;
    }
}
