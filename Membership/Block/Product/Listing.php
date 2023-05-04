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

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\DateUnit;
use Mageplaza\Membership\Model\Config\Source\DurationType;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;

/**
 * Class Listing
 * @package Mageplaza\Membership\Block\Product
 * @method setProduct(Product $product)
 */
class Listing extends AbstractProduct
{
    /**
     * @var string
     */
    protected $_template = 'product/listing.phtml';

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
     * Listing constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param DateUnit $dateUnit
     * @param MembershipFactory $membershipFactory
     * @param MembershipResource $membershipResource
     * @param array $data
     */
    public function __construct(
        Context $context,
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

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isFixedType()
    {
        return $this->getProduct()->getData(FieldRenderer::DURATION) !== DurationType::CUSTOM;
    }

    /**
     * @return array
     */
    public function getDurationOptions()
    {
        $product = $this->getProduct();

        $options = $product->getData(FieldRenderer::OPTIONS) ?: [];

        if (is_string($options)) {
            $options = Data::jsonDecode($options);
        }

        $membershipId = $product->getData(FieldRenderer::MEMBERSHIP);

        foreach ($options as &$option) {
            $unit = $this->dateUnit->getUnitByValue($option['unit']);

            $number = (float)$option['number'];

            $price = $this->helper->convertPrice($option['price'], true, false);

            $option['membership'] = $membershipId;
            $option['formattedPrice'] = $price;

            $option['label'] = $price . ' - ' . $number . ' ' . $unit;
            $option['data'] = $this->helper->jsonEncodeData($option);
        }

        return $options;
    }

    /**
     * @return string
     */
    public function getDurationData()
    {
        $product = $this->getProduct();

        $membershipId = $product->getData(FieldRenderer::MEMBERSHIP);

        $membership = $this->membershipFactory->create();

        $this->membershipResource->load($membership, $membershipId);

        $data = ['permanent' => __('Permanent')];

        if ($product->getData(FieldRenderer::DURATION) !== DurationType::PERMANENT) {
            $data = [
                'number' => (float)$membership->getDefaultDurationNo(),
                'unit' => $membership->getDefaultDurationUnit(),
            ];
        }

        $data['price'] = (float)$product->getData(FieldRenderer::PRICE);
        $data['membership'] = $membershipId;

        return $this->helper->jsonEncodeData($data);
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public function getDurationText($data)
    {
        return $this->helper->getDurationText($data);
    }
}
