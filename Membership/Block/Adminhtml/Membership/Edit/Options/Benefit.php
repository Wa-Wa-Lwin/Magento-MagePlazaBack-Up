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
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Membership;

/**
 * Class Benefit
 * @package Mageplaza\Membership\Block\Adminhtml\Membership\Edit\Options
 */
class Benefit extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_Membership::membership/benefit.phtml';

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * Benefit constructor.
     *
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
     * @return StoreInterface[]
     */
    public function getStores()
    {
        if (!$this->hasStores()) {
            $this->setData('stores', $this->_storeManager->getStores(true));
        }

        return $this->_getData('stores');
    }

    /**
     * Retrieve membership option values if membership input type select or multi-select
     *
     * @return array
     */
    public function getOptionValues()
    {
        $options = Data::jsonDecode($this->getObject()->getBenefit());

        if (empty($options['option'])) {
            return [];
        }

        return $this->_prepareOptionValues($options);
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function _prepareOptionValues($options)
    {
        $order = $options['option']['order'];
        $values = [];
        foreach ($options['option']['value'] as $id => $option) {
            $bunch = $this->_prepareMembershipOptionValues(
                $id,
                $option,
                $order[$id]
            );
            foreach ($bunch as $value) {
                $values[] = array_map('htmlspecialchars_decode', $value);
            }
        }

        return $values;
    }

    /**
     * Prepare option values of membership
     *
     * @param $optionId
     * @param array $option
     * @param $sortOrder
     *
     * @return array
     */
    protected function _prepareMembershipOptionValues($optionId, $option, $sortOrder)
    {
        $value['id'] = $optionId;
        $value['sort_order'] = $sortOrder;

        foreach ($this->getStores() as $store) {
            $storeId = $store->getId();
            if (isset($option[$storeId])) {
                $value['store' . $storeId] = $option[$storeId];
            }
        }

        return [$value];
    }

    /**
     * Returns stores sorted by Sort Order
     *
     * @return StoreInterface[]
     */
    public function getStoresSortedBySortOrder()
    {
        $stores = $this->getStores();
        if (is_array($stores)) {
            usort($stores, function ($storeA, $storeB) {
                /** @var Store $storeA */
                /** @var Store $storeB */
                if ($storeA->getSortOrder() === $storeB->getSortOrder()) {
                    return $storeA->getId() < $storeB->getId() ? -1 : 1;
                }

                return ($storeA->getSortOrder() < $storeB->getSortOrder()) ? -1 : 1;
            });
        }

        return $stores;
    }

    /**
     * @return Membership
     */
    protected function getObject()
    {
        return $this->_registry->registry('entity_membership');
    }
}
