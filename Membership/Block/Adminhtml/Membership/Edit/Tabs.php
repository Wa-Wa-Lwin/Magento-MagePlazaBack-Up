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

namespace Mageplaza\Membership\Block\Adminhtml\Membership\Edit;

use Exception;

/**
 * Class Tabs
 * @package Mageplaza\Membership\Block\Adminhtml\Membership\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('membership_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Membership Information'));
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _beforeToHtml()
    {
        $this->addTab('main', [
            'label' => __('General'),
            'title' => __('General'),
            'content' => $this->getChildHtml('main'),
            'active' => true
        ]);
        $this->addTab('display', [
            'label' => __('Display'),
            'title' => __('Display'),
            'content' => $this->getChildHtml('display')
        ]);
        $this->addTab('member', [
            'label' => __('Members'),
            'title' => __('Members'),
            'content' => $this->getChildHtml('member')
        ]);
        $this->addTab('history', [
            'label' => __('History'),
            'title' => __('History'),
            'content' => $this->getChildHtml('history')
        ]);

        return parent::_beforeToHtml();
    }
}
