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

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\Membership\Controller\Adminhtml\AbstractMembership;

/**
 * Class Edit
 * @package Mageplaza\Membership\Controller\Adminhtml\Membership
 */
class Edit extends AbstractMembership
{
    /**
     * @return Page|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $object = $this->_initMembership();
        $objId = $this->getRequest()->getParam('id');

        if ($objId !== null && $object->getData() === null) {
            $this->messageManager->addErrorMessage(__('This membership no longer exists.'));

            return $this->_redirect('*/*/');
        }

        // restore form data
        $data = $this->_session->getMembershipData(true);
        if (!empty($data)) {
            $object->addData($data);
        }

        $this->registry->register('entity_membership', $object);

        if ($object) {
            $pageTitle = $objId !== null ? __('Edit Membership #%1', $objId) : __('Create New Membership');
            $resultPage = $this->_initAction();
            $resultPage->getConfig()->getTitle()->prepend($pageTitle);

            return $resultPage;
        }

        return $this->_redirect('*/*/');
    }
}
