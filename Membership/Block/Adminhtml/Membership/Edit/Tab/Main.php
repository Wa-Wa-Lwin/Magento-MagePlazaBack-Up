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

namespace Mageplaza\Membership\Block\Adminhtml\Membership\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Price;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\Membership\Model\Config\Source\DateUnitFactory;
use Mageplaza\Membership\Model\Config\Source\MembershipStatusFactory;
use Mageplaza\Membership\Model\Membership;

/**
 * Class Main
 * @package Mageplaza\Membership\Block\Adminhtml\Membership\Edit\Tab
 */
class Main extends Generic
{
    /**
     * @var Membership
     */
    protected $_object;

    /**
     * @var DateUnitFactory
     */
    protected $dateUnitFactory;

    /**
     * @var MembershipStatusFactory
     */
    protected $membershipStatus;

    /**
     * Main constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param DateUnitFactory $dateUnitFactory
     * @param MembershipStatusFactory $membershipStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        DateUnitFactory $dateUnitFactory,
        MembershipStatusFactory $membershipStatus,
        array $data = []
    ) {
        $this->dateUnitFactory = $dateUnitFactory;
        $this->membershipStatus = $membershipStatus;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var Form $form */
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post'
            ]
        ]);

        $fieldset = $form->addFieldset('main_fieldset', ['legend' => __('General')]);

        $fieldset->addType('price', Price::class);

        $fieldset->addField('customer_group_id', 'hidden', ['name' => 'customer_group_id']);

        $fieldset->addField('customer_group_code', 'label', [
            'name' => 'customer_group_code',
            'label' => __('Customer Group'),
            'title' => __('Customer Group')
        ]);

        $fieldset->addField('status', 'select', [
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('Status'),
            'values' => $this->membershipStatus->create()->toOptionArray()
        ]);

        $fieldset->addField('level', 'text', [
            'name' => 'level',
            'label' => __('Level'),
            'title' => __('Level'),
            'class' => 'validate-digits',
            'note' => __("The membership packages with the higher levels than customers' current ones will be suggested to upgrade.")
        ]);

        $fieldset->addField('default_duration_unit', 'select', [
            'name' => 'default_duration_unit',
            'label' => __('Default Duration'),
            'title' => __('Default Duration'),
            'values' => $this->dateUnitFactory->create()->toOptionArray(),
            'before_element_html' => '<input id="default_duration_no" name="default_duration_no" value="' . $this->getDuration() . '" title="' . __('Duration No.') . '" class="validate-number validate-zero-or-greater input-text admin__control-text" type="text" style="width: 100px; margin-right: 5px" >'
        ]);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return string
     */
    protected function getDuration()
    {
        return number_format($this->getObject()->getData('default_duration_no'), 2, null, '');
    }

    /**
     * @inheritdoc
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getObject()->getData());

        return parent::_initFormValues();
    }

    /**
     * @return Membership
     */
    protected function getObject()
    {
        if (null === $this->_object) {
            return $this->_coreRegistry->registry('entity_membership');
        }

        return $this->_object;
    }
}
