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

namespace Mageplaza\Membership\Block\Adminhtml\Membership\Edit\Options;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Mageplaza\Membership\Helper\Data;

/**
 * Class Labels
 * @package Mageplaza\Membership\Block\Adminhtml\Membership\Edit\Options
 */
class Labels extends Template
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var string
     */
    protected $_template = 'Mageplaza_Membership::membership/labels.phtml';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return array
     */
    public function getStores()
    {
        $model = $this->_registry->registry('entity_membership');

        return $model->getStores();
    }

    /**
     * Retrieve frontend labels of membership for each store
     *
     * @return array
     */
    public function getLabelValues()
    {
        $attrObj = $this->_registry->registry('entity_membership');
        $storeLabels = Data::jsonDecode($attrObj->getName());
        $default = isset($storeLabels[0]) ? $storeLabels[0] : $attrObj->getData('customer_group_code');
        $values = [$default];

        foreach ($this->getStores() as $store) {
            $storeId = $store->getId();

            $values[$storeId] = isset($storeLabels[$storeId]) ? $storeLabels[$storeId] : '';
        }

        return $values;
    }
}
