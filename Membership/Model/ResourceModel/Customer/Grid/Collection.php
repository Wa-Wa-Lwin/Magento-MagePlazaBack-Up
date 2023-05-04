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

namespace Mageplaza\Membership\Model\ResourceModel\Customer\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * Class Collection
 * @package Mageplaza\Membership\Model\ResourceModel\Customer\Grid
 */
class Collection extends SearchResult
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'customer_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_membership_customer_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'membership_customer_collection';

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinRight(
            ['customer_entity' => $this->getTable('customer_entity')],
            'main_table.customer_id = customer_entity.entity_id'
        )->joinLeft(
            ['membership' => $this->getTable('mageplaza_membership_list')],
            'customer_entity.group_id = membership.membership_id',
            ['name']
        );

        $this->addFieldToFilter('main_table.status', ['notnull' => true]);

        return $this;
    }

    /**
     * @param string $field
     * @param null $condition
     *
     * @return SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'status') {
            $field = 'main_table.status';
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
