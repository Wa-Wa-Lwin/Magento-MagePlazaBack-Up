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

namespace Mageplaza\Membership\Test\Unit\Observer;

use Exception;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\Customer;
use Mageplaza\Membership\Model\CustomerFactory;
use Mageplaza\Membership\Observer\CustomerLoadAfter;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Class CustomerLoadAfterTest
 * @package Mageplaza\Membership\Test\Unit\Observer
 */
class CustomerLoadAfterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CustomerFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $customerFactory;

    /**
     * @var CustomerResource|PHPUnit_Framework_MockObject_MockObject
     */
    private $customerResource;

    /**
     * @var Data|PHPUnit_Framework_MockObject_MockObject
     */
    private $helper;

    /**
     * @var CustomerLoadAfter
     */
    private $object;

    /**
     *
     */
    protected function setUp()
    {
        $this->customerFactory = $this->getMockBuilder(CustomerFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->customerResource = $this->getMockBuilder(CustomerResource::class)
            ->disableOriginalConstructor()->getMock();
        $this->helper = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();

        $this->object = new CustomerLoadAfter(
            $this->customerFactory,
            $this->customerResource,
            $this->helper
        );
    }

    /**
     * @throws Exception
     */
    public function testExecuteWithExpiredCustomer()
    {
        /** @var Observer|PHPUnit_Framework_MockObject_MockObject $observer */
        $observer = $this->getMockBuilder(Observer::class)->disableOriginalConstructor()->getMock();

        $event = $this->getMockBuilder(Event::class)
            ->setMethods(['getCustomer'])
            ->disableOriginalConstructor()->getMock();
        $observer->method('getEvent')->willReturn($event);

        $object = $this->getMockBuilder(\Magento\Customer\Model\Customer::class)
            ->disableOriginalConstructor()->getMock();
        $event->expects($this->once())->method('getCustomer')->willReturn($object);

        $customer = $this->getMockBuilder(Customer::class)
            ->setMethods([
                'getResource',
                'attachCustomData',
                'getStartDate',
                'getDuration',
                'getStatus',
                'saveCustomData'
            ])
            ->disableOriginalConstructor()->getMock();
        $this->customerFactory->method('create')->willReturn($customer);

        $objectId = '12';
        $object->method('getId')->willReturn($objectId);

        $resource = $this->getMockBuilder(\Mageplaza\Membership\Model\ResourceModel\Customer::class)
            ->disableOriginalConstructor()->getMock();
        $customer->method('getResource')->willReturn($resource);
        $resource->method('load')->with($customer, $objectId)->willReturnSelf();
        $customer->expects($this->once())->method('attachCustomData')->with($object)->willReturnSelf();

        $startDate = date('Y/m/d', time() - 864000);
        $customer->method('getStartDate')->willReturn($startDate);

        $duration = 3600;
        $customer->method('getDuration')->willReturn($duration);

        $status = CustomerStatus::ACTIVE;
        $customer->method('getStatus')->willReturn($status);

        $groupId = 8;
        $object->method('getGroupId')->willReturn($groupId);

        $defaultGroup = 3;
        $this->helper->method('getDefaultGroup')->willReturn($defaultGroup);

        $data = [
            'last_membership_id' => $groupId,
            'status' => CustomerStatus::EXPIRED,
            'group_id' => $defaultGroup
        ];

        $customer->expects($this->once())->method('saveCustomData')->with($objectId, $data)->willReturnSelf();
        $object->method('addData')->with($data)->willReturnSelf();
        $this->customerResource->expects($this->once())->method('save')->with($object)->willReturnSelf();

        $this->object->execute($observer);
    }
}
