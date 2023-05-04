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
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Membership\Block\Product\View;
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
 * Class ViewTest
 * @package Mageplaza\Membership\Test\Unit\Block\Checkout
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ViewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Context|PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    /**
     * @var ArrayUtils|PHPUnit_Framework_MockObject_MockObject
     */
    private $arrayUtils;

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
     * @var StoreManagerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManager;

    /**
     * @var View
     */
    private $object;

    /**
     *
     */
    protected function setUp()
    {
        $this->context = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $this->arrayUtils = $this->getMockBuilder(ArrayUtils::class)->disableOriginalConstructor()->getMock();
        $this->helper = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $this->dateUnit = $this->getMockBuilder(DateUnit::class)->disableOriginalConstructor()->getMock();
        $this->membershipFactory = $this->getMockBuilder(MembershipFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->membershipResource = $this->getMockBuilder(MembershipResource::class)
            ->disableOriginalConstructor()->getMock();

        $this->registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
        $this->context->method('getRegistry')->willReturn($this->registry);

        $this->storeManager = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->context->method('getStoreManager')->willReturn($this->storeManager);

        $this->object = new View(
            $this->context,
            $this->arrayUtils,
            $this->helper,
            $this->dateUnit,
            $this->membershipFactory,
            $this->membershipResource,
            []
        );
    }

    public function testGetInformationWithPermanentMembership()
    {
        $product = $this->getMockBuilder(Product::class)->disableOriginalConstructor()->getMock();
        $this->registry->method('registry')->with('product')->willReturn($product);

        $typeInstance = $this->getMockBuilder(\Mageplaza\Membership\Model\Product\Type\Membership::class)
            ->disableOriginalConstructor()->getMock();
        $product->method('getTypeInstance')->willReturn($typeInstance);

        $count = 2;

        $membershipId = '12';
        $product->expects($this->at($count++))->method('getData')->with(FieldRenderer::MEMBERSHIP)
            ->willReturn($membershipId);

        $membership = $this->getMockBuilder(Membership::class)
            ->setMethods(['getDefaultDurationUnit', 'getDefaultDurationNo', 'getId'])
            ->disableOriginalConstructor()->getMock();
        $this->membershipFactory->expects($this->once())->method('create')->willReturn($membership);
        $this->membershipResource->expects($this->once())->method('load')->with($membership, $membershipId);

        $defaultDurationUnit = 'month';
        $membership->expects($this->once())->method('getDefaultDurationUnit')->willReturn($defaultDurationUnit);
        $durationNo = '10';
        $membership->expects($this->once())->method('getDefaultDurationNo')->willReturn($durationNo);
        $durationUnit = 'Month(s)';
        $this->dateUnit->method('getUnitByValue')->with($defaultDurationUnit)->willReturn($durationUnit);

        $duration = $durationNo ? $durationNo . ' ' . $durationUnit : __('Permanent');
        $isFixedPrice = true;
        $durationType = DurationType::PERMANENT;
        $product->expects($this->at($count++))->method('getData')->with(FieldRenderer::DURATION)
            ->willReturn($durationType);

        switch ($durationType) {
            case DurationType::PERMANENT:
                $duration = __('Permanent');
                break;
            case DurationType::CUSTOM:
                $isFixedPrice = false;
                $duration = null;
                break;
        }

        $options = [];
        $product->expects($this->at($count))->method('getData')->with(FieldRenderer::OPTIONS)
            ->willReturn($options);

        $result = [
            'duration' => $duration,
            'options' => $options,
            'isFixedPrice' => $isFixedPrice
        ];

        $this->assertEquals($result, $this->object->getInformation());
    }
}
