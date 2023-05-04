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

namespace Mageplaza\Membership\Block\Product;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Framework\Stdlib\ArrayUtils;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\DateUnit;
use Mageplaza\Membership\Model\Config\Source\DurationType;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;

/**
 * Class View
 * @package Mageplaza\Membership\Block\Product
 */
class View extends AbstractView
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var DateUnit
     */
    protected $dateUnit;

    /**
     * @var MembershipFactory
     */
    protected $membershipFactory;

    /**
     * @var MembershipResource
     */
    protected $membershipResource;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param Data $helper
     * @param DateUnit $dateUnit
     * @param MembershipFactory $membershipFactory
     * @param MembershipResource $membershipResource
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        Data $helper,
        DateUnit $dateUnit,
        MembershipFactory $membershipFactory,
        MembershipResource $membershipResource,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->dateUnit = $dateUnit;
        $this->membershipFactory = $membershipFactory;
        $this->membershipResource = $membershipResource;

        parent::__construct($context, $arrayUtils, $data);
    }

    /**
     * @return array
     */
    public function getInformation()
    {
        $product = $this->getProduct();

        $membership = $this->membershipFactory->create();
        $this->membershipResource->load($membership, $product->getData(FieldRenderer::MEMBERSHIP));

        $durationUnit = $this->dateUnit->getUnitByValue($membership->getDefaultDurationUnit());
        $durationNo = (float)$membership->getDefaultDurationNo();
        $duration = $durationNo ? $durationNo . ' ' . $durationUnit : __('Permanent');
        $isFixedPrice = true;

        switch ($product->getData(FieldRenderer::DURATION)) {
            case DurationType::PERMANENT:
                $duration = __('Permanent');
                break;
            case DurationType::CUSTOM:
                $isFixedPrice = false;
                $duration = null;
                break;
        }

        $options = $product->getData(FieldRenderer::OPTIONS) ?: [];
        foreach ($options as &$option) {
            $unit = $this->dateUnit->getUnitByValue($option['unit']);

            $number = (float)$option['number'];

            $price = $this->helper->convertPrice($option['price'], true, false);

            $option['membership'] = $membership->getId();
            $option['formattedPrice'] = $price;

            $option['label'] = $price . ' - ' . $number . ' ' . $unit;
            $option['data'] = Data::jsonEncode($option);
        }

        return [
            'duration' => $duration,
            'options' => $options,
            'isFixedPrice' => $isFixedPrice
        ];
    }
}
