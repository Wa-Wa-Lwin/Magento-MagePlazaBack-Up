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

namespace Mageplaza\Membership\Block\Adminhtml\Membership\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Mageplaza\Membership\Block\Adminhtml\Grid\Column\Renderer\EndDate;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\Membership;
use Mageplaza\Membership\Model\ResourceModel\Customer\Collection;
use Mageplaza\Membership\Model\ResourceModel\Customer\CollectionFactory;

/**
 * Class Member
 * @package Mageplaza\Membership\Block\Adminhtml\Membership\Edit\Tab
 */
class Member extends Extended
{
    /**
     * @var Membership
     */
    protected $_object;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Member constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param Registry $coreRegistry
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Registry $coreRegistry,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('member_grid');
        $this->setDefaultSort('customer_id');
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $objId = $this->getObject()->getData('customer_group_id');
        $collection->addFieldToFilter('group_id', $objId);
        $collection->addFieldToFilter(
            ['group_id', 'last_membership_id', 'inactive_membership_id'],
            [$objId, $objId, $objId]
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('customer_id', [
            'header' => __('ID'),
            'index' => 'customer_id',
            'type' => 'number'
        ]);

        $this->addColumn('email', [
            'header' => __('Customer'),
            'index' => 'email',
            'type' => 'text'
        ]);

        $this->addColumn('member_status', [
            'header' => __('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => CustomerStatus::getOptionArray()
        ]);

        $this->addColumn('start_date', [
            'header' => __('Activation Date'),
            'type' => 'datetime',
            'index' => 'start_date',
            'header_css_class' => 'col-date',
            'column_css_class' => 'col-date'
        ]);

        $this->addColumn('end_date', [
            'header' => __('Expiration Date'),
            'filter' => false,
            'sortable' => false,
            'type' => 'text',
            'index' => 'end_date',
            'header_css_class' => 'col-date',
            'column_css_class' => 'col-date',
            'renderer' => EndDate::class
        ]);

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/member', ['id' => $this->getObject()->getId()]);
    }

    /**
     * Return membership object
     *
     * @return Membership
     */
    protected function getObject()
    {
        if (null === $this->_object) {
            return $this->_coreRegistry->registry('entity_membership');
        }

        return $this->_object;
    }
}
