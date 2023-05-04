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

namespace Mageplaza\Membership\Model\ResourceModel\Customer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageplaza\Membership\Model\Customer;

/**
 * Class Collection
 * @package Mageplaza\Membership\Model\ResourceModel\Customer
 */
class Collection extends AbstractCollection
{
    /**
     * @type string
     */
    protected $_idFieldName = 'customer_id';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(Customer::class, \Mageplaza\Membership\Model\ResourceModel\Customer::class);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinRight(
            ['customer_entity' => $this->getTable('customer_entity')],
            'main_table.customer_id = customer_entity.entity_id'
        );

        return $this;
    }
}
