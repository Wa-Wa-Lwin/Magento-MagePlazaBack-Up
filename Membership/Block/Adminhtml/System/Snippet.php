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

namespace Mageplaza\Membership\Block\Adminhtml\System;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Snippet
 * @package Mageplaza\Membership\Block\Adminhtml\System
 */
class Snippet extends Field
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '<div style="padding-top: 8px">';

        $str = __('Use the following code to display Membership link at any places you want.');
        $html .= '<strong>' . $str . '</strong><br/><br/>';

        $str = '{{block class="Mageplaza\Membership\Block\Membership\Page"}}';
        $html .= '<strong>' . __('CMS Page, CMS Static Block') . '</strong><br/>';
        $html .= '<code style="background-color: #f5f5dc;">' . $str . '</code><br/><br/>';

        $str = $this->_escaper->escapeHtml(
            '<block class="Mageplaza\Membership\Block\Membership\Page" name="mpmembership_membership_page"/>'
        );
        $html .= '<strong>' . __('XML File, XML Data') . '</strong><br/>';
        $html .= '<code style="background-color: #f5f5dc;">' . $str . '</code><br/><br/>';

        $str = $this->_escaper->escapeHtml(
            '<?php echo $block->getLayout()->createBlock(\Mageplaza\Membership\Block\Membership\Page::class)->toHtml();?>'
        );
        $html .= '<strong>' . __('Template .phtml file') . '</strong><br/>';
        $html .= '<code style="background-color: #f5f5dc;">' . $str . '</code><br/>';

        $html .= '</div>';

        return $html . $element->getElementHtml();
    }
}
