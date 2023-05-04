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

namespace Mageplaza\Membership\Model\ResourceModel\Membership;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollections;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Mageplaza\Membership\Model\Membership;

/**
 * Class Collection
 * @package Mageplaza\Membership\Model\ResourceModel\Membership
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'membership_id';

    protected function _construct()
    {
        $this->_init(Membership::class, \Mageplaza\Membership\Model\ResourceModel\Membership::class);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinRight(
            ['customer_group' => $this->getTable('customer_group')],
            'main_table.membership_id = customer_group.customer_group_id'
        )->where('customer_group_id > 0');

        return $this;
    }
}
