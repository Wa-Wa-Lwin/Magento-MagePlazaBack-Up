<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageplaza\RewardPoints\Block\Account;

class CurrentLink extends \Magento\Customer\Block\Account\SortLink
{

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        $highlight = '';

        if ($this->getIsHighlighted()) {
            $highlight = ' current';
        }
        $className = strtolower($this->escapeHtml(__($this->getLabel())));

        switch ($className) {
            case 'ကျွန်ု၏ ပွိုင့်နှင့်ရမှတ်များ':
                $className = 'my points and rewards';
                break;
            case 'ရမှတ်များ':
                $className = 'reward dashboard';
                break;
            case 'လုပ်ဆောင်ချက်များ':
                $className = 'transactions';
                break;
            case 'ကျွန်ုပ် ရည်ညွှန်းချက်':
                $className = 'my referral';
                break;
            case 'ကျွန်ုပ်၏ မိုင်းစတုန်းများ':
                $className = 'my milestones';
                break;
            case 'နောက်သို့':
                $className = 'back';
                break;
        }


        if ($this->isCurrent()) {
            $html = '<li class="nav item current"><a href="' . $this->escapeHtml($this->getHref()) . '" class="' . $className . ' icon"';
            $html .= '<strong>'
                . $this->escapeHtml(__($this->getLabel()))
                . '</strong>';
            $html .= '</li>';
        } else {
            $html = '<li class="nav item' . $highlight . '"><a href="' . $this->escapeHtml($this->getHref()) . '" class="' . $className . ' icon"';
            $html .= $this->getTitle()
                ? ' title="' . $this->escapeHtml(__($this->getTitle())) . '"'
                : '';
            $html .= $this->getAttributesHtml() . '>';

            if ($this->getIsHighlighted()) {
                $html .= '<strong>';
            }

            $html .= $this->escapeHtml(__($this->getLabel()));

            if ($this->getIsHighlighted()) {
                $html .= '</strong>';
            }

            $html .= '</a></li>';
        }

        return $html;
    }

}
