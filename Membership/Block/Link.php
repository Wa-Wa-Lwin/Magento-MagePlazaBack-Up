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

namespace Mageplaza\Membership\Block;

use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Membership\Helper\Data;

/**
 * Class Link
 * @package Mageplaza\Membership\Block
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    const SORT_ORDER = 'sortOrder';

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_helper;

    /**
     * Link constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_helper->isShowOnToplink()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('membership/dashboard');
    }

    /**
     * @return Phrase
     */
    public function getLabel()
    {
        return __('Membership');
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }
}
