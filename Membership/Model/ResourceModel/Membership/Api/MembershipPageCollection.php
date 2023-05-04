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

namespace Mageplaza\Membership\Model\ResourceModel\Membership\Api;

use Mageplaza\Membership\Model\Api\MembershipPage;
use Mageplaza\Membership\Model\Config\Source\MembershipStatus;
use Mageplaza\Membership\Model\ResourceModel\Membership;
use Mageplaza\Membership\Model\ResourceModel\Membership\Collection;

/**
 * Class Collection
 * @package Mageplaza\Membership\Model\ResourceModel\Membership
 */
class MembershipPageCollection extends Collection
{

    protected function _construct()
    {
        $this->_init(MembershipPage::class, Membership::class);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addFieldToFilter('default_product', ['gt' => 0])
            ->addFieldToFilter('status', MembershipStatus::ACTIVE)
            ->setOrder('sort_order', 'asc');

        return $this;
    }
}
