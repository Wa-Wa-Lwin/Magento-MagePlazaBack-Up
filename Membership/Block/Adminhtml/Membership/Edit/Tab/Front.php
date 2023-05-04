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
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\Membership;
use Mageplaza\Membership\Model\Product\Type\Membership as MembershipProduct;

/**
 * Class Front
 * @package Mageplaza\Membership\Block\Adminhtml\Membership\Edit\Tab
 */
class Front extends Generic
{
    /**
     * @var Membership
     */
    protected $_object;

    /**
     * @var YesnoFactory
     */
    protected $yesnoFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Front constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param YesnoFactory $yesnoFactory
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        YesnoFactory $yesnoFactory,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->yesnoFactory = $yesnoFactory;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('front_fieldset', ['legend' => __('Display Configuration')]);

        $fieldset->addField('image', 'image', [
            'name' => 'image',
            'label' => __('Image'),
            'title' => __('Image')
        ]);

        $fieldset->addField('background_color', 'text', [
            'name' => 'background_color',
            'label' => __('Background Color'),
            'title' => __('Background Color'),
            'class' => 'jscolor {hash:true,refine:false}'
        ]);

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('type_id', MembershipProduct::TYPE_MEMBERSHIP)
            ->addFieldToFilter(FieldRenderer::MEMBERSHIP, $this->getObject()->getData('customer_group_id'))
            ->addFieldToFilter('status', 1)
            ->addAttributeToSelect('name');

        $products = [['value' => '', 'label' => __('-- Please select a product --')]];
        foreach ($collection->getItems() as $item) {
            $products[] = [
                'value' => $item->getData('entity_id'),
                'label' => $item->getData('name')
            ];
        }
        $fieldset->addField('default_product', 'select', [
            'name' => 'default_product',
            'label' => __('Default Product'),
            'title' => __('Default Product'),
            'values' => $products
        ]);

        $yesno = $this->yesnoFactory->create()->toOptionArray();
        $fieldset->addField('is_featured', 'select', [
            'name' => 'is_featured',
            'label' => __('Is Featured'),
            'title' => __('Is Featured'),
            'values' => $yesno
        ]);

        $fieldset->addField('featured_label', 'text', [
            'name' => 'featured_label',
            'label' => __('Featured Label'),
            'title' => __('Featured Label'),
            'note' => __('The label should not be too long to make sure it will fully appear at the frontend.<br/>If empty, the default label "Featured" will be used.')
        ]);

        $fieldset->addField('sort_order', 'text', [
            'name' => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'class' => 'validate-digits',
            'note' => __('Default is 0. The one with the smallest order will display first.')
        ]);

        $this->setForm($form);

        return parent::_prepareForm();
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
     * Return membership object
     *
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
