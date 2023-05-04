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
use Magento\Store\Model\Store;
use Mageplaza\Membership\Block\Adminhtml\Grid\Column\Renderer\Duration;
use Mageplaza\Membership\Model\Config\Source\HistoryAction;
use Mageplaza\Membership\Model\Membership;
use Mageplaza\Membership\Model\ResourceModel\History\Collection;
use Mageplaza\Membership\Model\ResourceModel\History\CollectionFactory;

/**
 * Class History
 * @package Mageplaza\Membership\Block\Adminhtml\Membership\Edit\Tab
 */
class History extends Extended
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
     * History constructor.
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

        $this->setId('history_grid');
        $this->setDefaultSort('history_id');
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('customer_group_id', $this->getObject()->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('history_id', [
            'header' => __('ID'),
            'index' => 'history_id',
            'type' => 'number'
        ]);

        $this->addColumn('email', [
            'header' => __('Customer'),
            'index' => 'email',
            'type' => 'text'
        ]);

        $this->addColumn('action', [
            'header' => __('Action'),
            'index' => 'action',
            'type' => 'options',
            'options' => HistoryAction::getOptionArray()
        ]);

        /** @var Store $store */
        $store = $this->_storeManager->getStore();

        $this->addColumn('amount', [
            'header' => __('Amount'),
            'align' => 'right',
            'index' => 'amount',
            'type' => 'price',
            'currency_code' => $store->getBaseCurrencyCode()
        ]);

        $this->addColumn('history_duration', [
            'header' => __('Duration'),
            'filter' => false,
            'sortable' => false,
            'align' => 'right',
            'index' => 'history_duration',
            'type' => 'text',
            'renderer' => Duration::class
        ]);

        $this->addColumn('created_date', [
            'header' => __('Purchased Date'),
            'type' => 'datetime',
            'index' => 'created_date',
            'header_css_class' => 'col-date',
            'column_css_class' => 'col-date'
        ]);

        $this->addColumn('membership_start', [
            'header' => __('Activation Date'),
            'type' => 'datetime',
            'index' => 'membership_start',
            'header_css_class' => 'col-date',
            'column_css_class' => 'col-date'
        ]);

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/history', ['id' => $this->getObject()->getId()]);
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
