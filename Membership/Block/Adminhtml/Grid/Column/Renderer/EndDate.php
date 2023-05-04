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

namespace Mageplaza\Membership\Block\Adminhtml\Grid\Column\Renderer;

use IntlDateFormatter;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class EndDate
 * @package Mageplaza\Membership\Block\Adminhtml\Grid\Column\Renderer
 */
class EndDate extends AbstractRenderer
{
    /**
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        if (!$startDate = $row->getData('start_date')) {
            return parent::render($row);
        }

        $duration = (int)$row->getData('duration');

        if ($duration === 0) {
            $row->setData('end_date', __('Permanent'));

            return parent::render($row);
        }

        $endDate = strtotime($startDate) + $duration;
        $row->setData('end_date', $this->formatTime(date('Y/m/d h:i:s', $endDate), IntlDateFormatter::MEDIUM, true));

        return parent::render($row);
    }
}
