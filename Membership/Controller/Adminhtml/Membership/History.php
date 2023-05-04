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

namespace Mageplaza\Membership\Controller\Adminhtml\Membership;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\GroupFactory;
use Magento\Customer\Model\ResourceModel\Group as GroupResource;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\Membership\Controller\Adminhtml\AbstractMembership;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;

/**
 * Class History
 * @package Mageplaza\Membership\Controller\Adminhtml\Membership
 */
class History extends AbstractMembership
{
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * History constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Filter $filter
     * @param Data $helper
     * @param GroupFactory $groupFactory
     * @param GroupResource $groupResource
     * @param MembershipFactory $membershipFactory
     * @param MembershipResource $membershipResource
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Filter $filter,
        Data $helper,
        GroupFactory $groupFactory,
        GroupResource $groupResource,
        MembershipFactory $membershipFactory,
        MembershipResource $membershipResource,
        LayoutFactory $resultLayoutFactory
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $filter,
            $helper,
            $groupFactory,
            $groupResource,
            $membershipFactory,
            $membershipResource
        );
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $membership = $this->membershipFactory->create();
        $objId = $this->getRequest()->getParam('id');

        if ($objId !== null) {
            $this->membershipResource->load($membership, $objId);
            $group = $this->groupFactory->create();
            $this->groupResource->load($group, $objId);
            $membership->addData($group->getData());
        }

        $this->registry->register('entity_membership', $membership);

        return $this->resultLayoutFactory->create();
    }
}
