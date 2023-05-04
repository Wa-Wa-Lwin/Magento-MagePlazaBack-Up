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

namespace Mageplaza\Membership\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Membership\Helper\Account;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;

/**
 * Class Upgrade
 * @package Mageplaza\Membership\Controller\Index
 */
class Upgrade extends Action
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Account
     */
    protected $accountHelper;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Upgrade constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param Account $accountHelper
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Data $helper,
        Account $accountHelper,
        PageFactory $resultPageFactory
    ) {
        $this->helper = $helper;
        $this->accountHelper = $accountHelper;
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * @return Page
     * @throws NotFoundException
     */
    public function execute()
    {
        if (!$this->helper->isEnabled() || !$this->helper->isAllowUpgrade() || !$this->canUpgrade()) {
            throw new NotFoundException(__('Cannot upgrade membership.'));
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Upgrade Your Membership'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function canUpgrade()
    {
        $customer = $this->accountHelper->getCurrentCustomer();

        return $customer && (int)$customer->getStatus() === CustomerStatus::ACTIVE;
    }
}
