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

namespace Mageplaza\Membership\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\ResourceModel\Membership\CollectionFactory;

/**
 * Class MembershipOptions
 * @package Mageplaza\Membership\Model\Config\Source
 */
class MembershipOptions extends AbstractSource
{
    /**
     * @var array
     */
    protected $_memberships;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MembershipOptions constructor.
     *
     * @param Data $helper
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Data $helper,
        CollectionFactory $collectionFactory
    ) {
        $this->helper = $helper;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_memberships === null) {
            $this->_memberships = [['value' => '', 'label' => __('-- Please select an option --')]];

            $collection = $this->collectionFactory->create();

            $data = $collection->getData();

            usort($data, function ($valueA, $valueB) {
                return ($valueA['customer_group_id'] <= $valueB['customer_group_id']) ? -1 : 1;
            });

            foreach ($data as $item) {
                $name = Data::jsonDecode($item['name']);
                $label = empty($name[0]) ? $item['customer_group_code'] : $name[0];

                $this->_memberships[] = ['value' => $item['customer_group_id'], 'label' => $label];
            }
        }

        return $this->_memberships;
    }
}
