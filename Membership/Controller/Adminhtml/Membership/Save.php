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

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\GroupFactory;
use Magento\Customer\Model\ResourceModel\Group as GroupResource;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\Membership\Controller\Adminhtml\AbstractMembership;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;

/**
 * Class Save
 * @package Mageplaza\Membership\Controller\Adminhtml\Membership
 */
class Save extends AbstractMembership
{
    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * Save constructor.
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
     * @param UploaderFactory $uploaderFactory
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
        UploaderFactory $uploaderFactory
    ) {
        $this->uploaderFactory = $uploaderFactory;

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
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        /** @var Http $request */
        $request = $this->getRequest();

        if ($data = $request->getPostValue()) {
            if (!empty($data['serialized_options']) && $this->helper->versionCompare('2.2.6')) {
                $options = [];
                foreach (Data::jsonDecode($data['serialized_options']) as $option) {
                    parse_str($option, $option);
                    $options[] = $option;
                }

                $data = array_merge_recursive($data, ...$options);
            }

            $objId = $data['customer_group_id'];

            if (isset($data['option'])) {
                foreach ($data['option']['value'] as $key => $value) {
                    if (empty($value[0])) {
                        $this->messageManager->addErrorMessage(__("The value of Admin scope can't be empty in Benefit(s)."));

                        return $this->_redirect('*/*/edit', ['id' => $objId, '_current' => true]);
                    }
                }
            }

            $this->_uploadImages($data);
            $object = $this->_initMembership();
            $this->_processData($data);
            $object->addData($data);

            try {
                $object->setId($objId);
                $this->membershipResource->save($object);

                $this->messageManager->addSuccessMessage(__('The membership has been saved.'));

                $this->_session->setMembershipData(false);

                if ($this->getRequest()->getParam('back', false)) {
                    return $this->_redirect('*/*/edit', ['id' => $objId, '_current' => true]);
                }
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                $this->_session->setMembershipData($data);

                return $this->_redirect('*/*/edit', ['id' => $objId, '_current' => true]);
            }
        }

        return $this->_redirect('*/*/');
    }

    /**
     * @param $data
     *
     * @return $this
     */
    protected function _uploadImages(&$data)
    {
        if (empty($data['image']['delete'])) {
            try {
                $uploader = $this->uploaderFactory->create(['fileId' => 'image']);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);

                $image = $uploader->save($this->mediaDirectory->getAbsolutePath(Data::TEMPLATE_MEDIA_PATH));

                $data['image'] = Data::TEMPLATE_MEDIA_PATH . '/' . $image['file'];
            } catch (Exception $e) {
                $data['image'] = isset($data['image']['value']) ? $data['image']['value'] : '';
            }
        } else {
            $data['image'] = '';
        }

        return $this;
    }

    /**
     * @param $data
     *
     * @return $this
     */
    protected function _processData(&$data)
    {
        if (!empty($data['labels'])) {
            $data['name'] = Data::jsonEncode($data['labels']);
        }

        if (!empty($data['option'])) {
            foreach (array_keys($data['option']['value']) as $index) {
                if (!empty($data['option']['delete'][$index])) {
                    unset($data['option']['value'][$index]);
                }
            }
            $options['option'] = $data['option'];

            $data['benefit'] = Data::jsonEncode($options);
        }

        return $this;
    }
}
