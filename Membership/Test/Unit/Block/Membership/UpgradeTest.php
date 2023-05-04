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

namespace Mageplaza\Membership\Test\Unit\Block\Membership;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Membership\Block\Membership\Upgrade;
use Mageplaza\Membership\Helper\Account;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\Config\Source\UpgradingCost;
use Mageplaza\Membership\Model\Customer;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;
use Mageplaza\Membership\Model\ResourceModel\Membership\CollectionFactory;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Class UpgradeTest
 * @package Mageplaza\Membership\Test\Unit\Block\Membership
 */
class UpgradeTest extends PHPUnit_Framework_TestCase
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
     * @var Account|PHPUnit_Framework_MockObject_MockObject
     */
    private $accountHelper;

    /**
     * @var MembershipFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $membershipFactory;

    /**
     * @var MembershipResource|PHPUnit_Framework_MockObject_MockObject
     */
    private $membershipResource;

    /**
     * @var ProductRepository|PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepository;

    /**
     * @var FormKey|PHPUnit_Framework_MockObject_MockObject
     */
    private $formKey;

    /**
     * @var CollectionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionFactory;

    /**
     * @var Upgrade
     */
    private $object;

    /**
     *
     */
    protected function setUp()
    {
        $this->context = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $this->helper = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $this->accountHelper = $this->getMockBuilder(Account::class)->disableOriginalConstructor()->getMock();
        $this->membershipFactory = $this->getMockBuilder(MembershipFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->membershipResource = $this->getMockBuilder(MembershipResource::class)
            ->disableOriginalConstructor()->getMock();
        $this->productRepository = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()->getMock();
        $this->formKey = $this->getMockBuilder(FormKey::class)->disableOriginalConstructor()->getMock();
        $this->collectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();

        $this->object = new Upgrade(
            $this->context,
            $this->helper,
            $this->accountHelper,
            $this->membershipFactory,
            $this->membershipResource,
            $this->productRepository,
            $this->formKey,
            $this->collectionFactory,
            []
        );
    }

    public function testGetDeductAmount()
    {
        $customer = $this->getMockBuilder(Customer::class)
            ->setMethods(['getStatus', 'getDuration', 'getStartDate', 'getMembershipPrice'])
            ->disableOriginalConstructor()->getMock();
        $this->accountHelper->expects($this->once())->method('getCurrentCustomer')->willReturn($customer);

        $upgradingType = UpgradingCost::DEDUCT_REMAIN;
        $this->helper->expects($this->once())->method('getUpgradingCost')->willReturn($upgradingType);

        $customer->method('getStatus')->willReturn(CustomerStatus::ACTIVE);
        $duration = 7200;
        $customer->method('getDuration')->willReturn($duration);

        $startDate = date('Y/m/d', time() - 3600);
        $customer->method('getStartDate')->willReturn($startDate);

        $remaining = strtotime($startDate) + $duration - time();
        $remaining -= $remaining % 3600;

        $rate = $remaining / $duration;

        $price = 20;
        $customer->method('getMembershipPrice')->willReturn($price);

        $this->assertEquals($price * $rate, $this->object->getDeductAmount());
    }
}
