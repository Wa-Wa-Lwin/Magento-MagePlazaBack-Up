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

namespace Mageplaza\Membership\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface MembershipRepositoryInterface
 * @package Mageplaza\Membership\Api
 */
interface MembershipRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria The search criteria.
     *
     * @return \Mageplaza\Membership\Api\Data\MembershipSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria The search criteria.
     *
     * @return \Mageplaza\Membership\Api\Data\MembershipPageSearchResultInterface
     */
    public function getMembershipPage(SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria The search criteria.
     *
     * @return \Mageplaza\Membership\Api\Data\MembershipPageSearchResultInterface
     */
    public function getUpgradePage($customerId, SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param int $customerId
     *
     * @return \Mageplaza\Membership\Api\Data\MembershipInterface
     */
    public function getCurrentMembership($customerId);
}
