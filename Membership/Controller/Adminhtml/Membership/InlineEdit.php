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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\Membership\Model\Membership;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;
use RuntimeException;

/**
 * Class InlineEdit
 * @package Mageplaza\Membership\Controller\Adminhtml\Membership
 */
class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var MembershipResource
     */
    protected $membershipResource;

    /**
     * @var MembershipFactory
     */
    protected $membershipFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param MembershipResource $membershipResource
     * @param MembershipFactory $membershipFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        MembershipResource $membershipResource,
        MembershipFactory $membershipFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->membershipResource = $membershipResource;
        $this->membershipFactory = $membershipFactory;
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $items = $this->getRequest()->getParam('items', []);

        if (empty($items) && !$this->getRequest()->getParam('isAjax')) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($items) as $objId) {
            /** @var Membership $object */
            $object = $this->membershipFactory->create();
            $this->membershipResource->load($object, $objId);

            try {
                $object->addData($items[$objId]);
                $this->membershipResource->save($object);
            } catch (RuntimeException $e) {
                $messages[] = $this->getErrorWithRuleId($object, $e->getMessage());
                $error = true;
            } catch (Exception $e) {
                $messages[] = $this->getErrorWithRuleId(
                    $object,
                    __('Something went wrong while saving the membership.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add object id to error message
     *
     * @param Membership $object
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithRuleId(Membership $object, $errorText)
    {
        return '[Membership ID: ' . $object->getId() . '] ' . $errorText;
    }
}
