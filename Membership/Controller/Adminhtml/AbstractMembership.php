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

namespace Mageplaza\Membership\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\GroupFactory;
use Magento\Customer\Model\ResourceModel\Group as GroupResource;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Membership;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;

/**
 * Class AbstractMembership
 * @package Mageplaza\Membership\Controller\Adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractMembership extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Mageplaza_Membership::membership';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var GroupFactory
     */
    protected $groupFactory;

    /**
     * @var GroupResource
     */
    protected $groupResource;

    /**
     * @var MembershipFactory
     */
    protected $membershipFactory;

    /**
     * @var MembershipResource
     */
    protected $membershipResource;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * AbstractMembership constructor.
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
        MembershipResource $membershipResource
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->filter = $filter;
        $this->helper = $helper;
        $this->groupFactory = $groupFactory;
        $this->groupResource = $groupResource;
        $this->membershipFactory = $membershipFactory;
        $this->membershipResource = $membershipResource;
        $this->mediaDirectory = $this->helper->getMediaDirectory();

        parent::__construct($context);
    }

    /**
     * Load layout, set breadcrumbs
     *
     * @return Page
     */
    protected function _initAction()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);

        return $resultPage;
    }

    /**
     * Initialize membership object
     *
     * @return Membership|Group
     */
    protected function _initMembership()
    {
        $membership = $this->membershipFactory->create();
        $objId = $this->getRequest()->getParam('id');

        if ($objId !== null) {
            $this->membershipResource->load($membership, $objId);
            $group = $this->groupFactory->create();
            $this->groupResource->load($group, $objId);
            $membership->addData($group->getData());
        }

        return $membership;
    }
}
