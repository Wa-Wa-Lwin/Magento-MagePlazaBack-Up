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

namespace Mageplaza\Membership\Block\Adminhtml\Membership;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Customer\Model\Group;
use Magento\Framework\Registry;
use Mageplaza\Membership\Model\Membership;

/**
 * Class Edit
 * @package Mageplaza\Membership\Block\Adminhtml\Membership
 */
class Edit extends Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Mageplaza_Membership';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_membership';

    /**
     * Core registry
     *
     * @var Registry
     */
    public $_coreRegistry;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * @return Membership|Group
     */
    protected function getMembership()
    {
        return $this->_coreRegistry->registry('entity_membership');
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        parent::_construct();

        $this->buttonList->add(
            'save_and_continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]
                ]
            ]
        );

        $this->buttonList->remove('delete');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        $objId = $this->getMembership()->getId();

        return $objId !== null ? __('Edit Membership #%1', $objId) : __('Create New Membership');
    }
}
