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

namespace Mageplaza\Membership\Test\Unit\Block\Checkout;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Mageplaza\Membership\Block\Product\Listing;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\DateUnit;
use Mageplaza\Membership\Model\Config\Source\DurationType;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\Membership;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Class ListingTest
 * @package Mageplaza\Membership\Test\Unit\Block\Checkout
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ListingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Context|PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    /**
     * @var Data|PHPUnit_Framework_MockObject_MockObject
     */
    private $helper;

    /**
     * @var DateUnit|PHPUnit_Framework_MockObject_MockObject
     */
    private $dateUnit;

    /**
     * @var MembershipFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $membershipFactory;

    /**
     * @var MembershipResource|PHPUnit_Framework_MockObject_MockObject
     */
    private $membershipResource;

    /**
     * @var Registry|PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    /**
     * @var Listing
     */
    private $object;

    /**
     *
     */
    protected function setUp()
    {
        $this->context = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $this->helper = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $this->dateUnit = $this->getMockBuilder(DateUnit::class)->disableOriginalConstructor()->getMock();
        $this->membershipFactory = $this->getMockBuilder(MembershipFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->membershipResource = $this->getMockBuilder(MembershipResource::class)
            ->disableOriginalConstructor()->getMock();

        $this->registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
        $this->context->method('getRegistry')->willReturn($this->registry);

        $this->object = new Listing(
            $this->context,
            $this->helper,
            $this->dateUnit,
            $this->membershipFactory,
            $this->membershipResource
        );
    }

    public function testGetDurationOptions()
    {
        $options = [
            [
                'unit' => 'month',
                'number' => 1,
                'price' => 10
            ]
        ];

        $product = $this->getMockBuilder(Product::class)->disableOriginalConstructor()->getMock();
        $this->registry->method('registry')->with('product')->willReturn($product);

        $product->expects($this->at(0))->method('getData')->with(FieldRenderer::OPTIONS)->willReturn($options);

        $membershipId = '12';
        $product->expects($this->at(1))->method('getData')->with(FieldRenderer::MEMBERSHIP)->willReturn($membershipId);

        foreach ($options as &$option) {
            $unit = 'Month(s)';
            $this->dateUnit->method('getUnitByValue')->with($option['unit'])->willReturn($unit);

            $number = (float)$option['number'];

            $price = '$12';

            $this->helper->method('convertPrice')->with($option['price'])->willReturn($price);

            $option['membership'] = $membershipId;
            $option['formattedPrice'] = $price;
            $option['label'] = $price . ' - ' . $number . ' ' . $unit;

            $data = $option;
            $this->helper->method('jsonEncodeData')->with($option)->willReturn($data);

            $option['data'] = $data;
        }

        unset($option);

        $this->assertEquals($options, $this->object->getDurationOptions());
    }

    public function testGetDurationData()
    {
        $product = $this->getMockBuilder(Product::class)->disableOriginalConstructor()->getMock();
        $this->registry->method('registry')->with('product')->willReturn($product);

        $membershipId = '12';
        $product->expects($this->at(0))->method('getData')->with(FieldRenderer::MEMBERSHIP)->willReturn($membershipId);

        $membership = $this->getMockBuilder(Membership::class)->disableOriginalConstructor()->getMock();
        $this->membershipFactory->expects($this->once())->method('create')->willReturn($membership);
        $this->membershipResource->expects($this->once())->method('load')->with($membership, $membershipId);

        $data = ['permanent' => __('Permanent')];

        $duration = DurationType::PERMANENT;
        $product->expects($this->at(1))->method('getData')->with(FieldRenderer::DURATION)->willReturn($duration);

        $price = '10';
        $product->expects($this->at(2))->method('getData')->with(FieldRenderer::PRICE)->willReturn($price);
        $data['price'] = (float)$price;

        $data['membership'] = $membershipId;

        $this->helper->method('jsonEncodeData')->with($data)->willReturn($data);

        $this->assertEquals($data, $this->object->getDurationData());
    }
}
