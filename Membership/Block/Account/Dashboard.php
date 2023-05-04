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

namespace Mageplaza\Membership\Block\Account;

use Magento\Framework\View\Element\Template;
use Mageplaza\Membership\Helper\Account;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\HistoryAction;

/**
 * Class Dashboard
 * @package Mageplaza\Membership\Block\Account
 */
class Dashboard extends Template
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Account
     */
    protected $accountHelper;

    /**
     * Dashboard constructor.
     *
     * @param Template\Context $context
     * @param Data $helper
     * @param Account $accountHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        Account $accountHelper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->accountHelper = $accountHelper;

        parent::__construct($context, $data);
    }

    /**
     * @param float $price
     *
     * @return float
     */
    public function convertPrice($price)
    {
        return $this->helper->convertPrice($price, true, false);
    }

    /**
     * @param int $status
     *
     * @return string
     */
    public function getActionLabel($status)
    {
        $statusArray = HistoryAction::getOptionArray();

        if (array_key_exists($status, $statusArray)) {
            return $statusArray[$status];
        }

        return '';
    }
}
