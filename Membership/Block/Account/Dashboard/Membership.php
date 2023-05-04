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

namespace Mageplaza\Membership\Block\Account\Dashboard;

use IntlDateFormatter;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Mageplaza\Membership\Block\Account\Dashboard;
use Mageplaza\Membership\Block\Membership\Upgrade;
use Mageplaza\Membership\Helper\Account;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\Config\Source\DateUnit;
use Mageplaza\Membership\Model\Config\Source\DurationType;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;

/**
 * Class Membership
 * @package Mageplaza\Membership\Block\Account\Dashboard
 */
class Membership extends Dashboard
{
    /**
     * @var MembershipFactory
     */
    protected $membershipFactory;

    /**
     * @var MembershipResource
     */
    protected $membershipResource;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * Membership constructor.
     *
     * @param Template\Context $context
     * @param Data $helper
     * @param Account $accountHelper
     * @param MembershipFactory $membershipFactory
     * @param MembershipResource $membershipResource
     * @param ProductRepository $productRepository
     * @param FormKey $formKey
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        Account $accountHelper,
        MembershipFactory $membershipFactory,
        MembershipResource $membershipResource,
        ProductRepository $productRepository,
        FormKey $formKey,
        array $data = []
    ) {
        $this->membershipFactory = $membershipFactory;
        $this->membershipResource = $membershipResource;
        $this->productRepository = $productRepository;
        $this->formKey = $formKey;

        parent::__construct($context, $helper, $accountHelper, $data);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * @return \Mageplaza\Membership\Model\Membership
     * @throws NoSuchEntityException
     */
    public function getMembership()
    {
        return $this->membershipFactory->create()->getCurrentMembership($this->accountHelper->getCurrentCustomer());
    }

    /**
     * @param \Mageplaza\Membership\Model\Membership $membership
     *
     * @return string
     * @throws LocalizedException
     */
    public function getMembershipName($membership)
    {
        $storeId = $this->helper->getScopeId();
        $value = Data::jsonDecode($membership->getName());

        if (!empty($value[$storeId])) {
            return $value[$storeId];
        }

        return empty($value[0]) ? $membership->getData('customer_group_code') : $value[0];
    }

    /**
     * @return string
     */
    public function getExpiredDate()
    {
        $customer = $this->accountHelper->getCurrentCustomer();

        if (!$customer || !$start = $customer->getStartDate()) {
            return '';
        }

        $duration = (int)$customer->getDuration();

        if ($duration === 0) {
            return __('Permanent');
        }

        $timestamp = strtotime($start) + $duration;

        return $this->formatTime(date('Y/m/d h:i:s', $timestamp), IntlDateFormatter::MEDIUM, true);
    }

    /**
     * @param \Mageplaza\Membership\Model\Membership $membership
     *
     * @return array
     * @throws LocalizedException
     */
    public function getBenefit($membership)
    {
        $storeId = $this->helper->getScopeId();
        $value = Data::jsonDecode($membership->getBenefit());

        if (empty($value['option']['value'])) {
            return [];
        }

        $result = [];

        foreach ($value['option']['value'] as $index => $item) {
            $result[$index] = $item[0];

            if (!empty($item[$storeId])) {
                $result[$index] = $item[$storeId];
            }
        }

        return $result;
    }

    /**
     * @param \Mageplaza\Membership\Model\Membership $membership
     *
     * @return string
     */
    public function getMembershipImage($membership)
    {
        $image = $membership->getImage();

        return $image ? $this->helper->getMediaUrl($image, false) : false;
    }

    /**
     * @return string
     */
    public function getMembershipFeaturedImage()
    {
        return $this->helper->getFeaturedImageUrl();
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function canUpgrade()
    {
        if ($this->getCustomerStatus() !== CustomerStatus::ACTIVE || !$this->helper->isAllowUpgrade()) {
            return false;
        }

        $upgradeLevelCollection = $this->getLayout()->createBlock(Upgrade::class)
            ->getMembershipCollection();
        if (empty($upgradeLevelCollection)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function canRenew()
    {
        return $this->getCustomerStatus() !== CustomerStatus::ACTIVE;
    }

    /**
     * @return int
     */
    public function getCustomerStatus()
    {
        if (!$customer = $this->accountHelper->getCurrentCustomer()) {
            return 0;
        }

        return (int)$customer->getStatus();
    }

    /**
     * @return string
     */
    public function getUpgradeUrl()
    {
        return $this->_urlBuilder->getUrl('membership/index/upgrade');
    }

    /**
     * @param \Mageplaza\Membership\Model\Membership $membership
     *
     * @return string
     */
    public function getBuyUrl($membership)
    {
        return $this->_urlBuilder->getUrl('checkout/cart/add', ['product' => $membership->getDefaultProduct()]);
    }

    /**
     * @param \Mageplaza\Membership\Model\Membership $membership
     *
     * @return Product|null
     */
    public function getProduct($membership)
    {
        try {
            return $this->productRepository->getById($membership->getDefaultProduct());
        } catch (NoSuchEntityException $e) {
            $this->_logger->critical($e->getMessage());
        }

        return null;
    }

    /**
     * @param \Mageplaza\Membership\Model\Membership $membership
     * @param int $toDeduct
     *
     * @return array
     */
    public function getInformation($membership, $toDeduct = 0)
    {
        if (!$product = $this->getProduct($membership)) {
            return [
                'duration' => '',
                'options' => [],
                'isFixedPrice' => false
            ];
        }

        $membership = $this->membershipFactory->create();
        $this->membershipResource->load($membership, $product->getData(FieldRenderer::MEMBERSHIP));

        $durationUnit = DateUnit::getUnitStatic($membership->getDefaultDurationUnit());
        $durationNo = (float)$membership->getDefaultDurationNo();
        $duration = $durationNo ? $durationNo . ' ' . $durationUnit : __('Permanent');
        $isFixedPrice = true;

        switch ($product->getData(FieldRenderer::DURATION)) {
            case DurationType::PERMANENT:
                $options = [];
                $duration = __('Permanent');
                break;
            case DurationType::CUSTOM:
                $options = $product->getData(FieldRenderer::OPTIONS);
                $isFixedPrice = false;
                $duration = null;
                break;
            default:
                $options = [];
        }

        foreach ($options as &$option) {
            $price = max(0, $option['price'] - $toDeduct);
            $option['price'] = $price;
            $price = $this->helper->convertPrice($price, true, false);

            $option['membership'] = $membership->getId();
            $option['formattedPrice'] = $price;

            $option['label'] = $price . ' - ' . $this->helper->getDurationText(Data::jsonEncode($option));
            $option['data'] = Data::jsonEncode($option);
        }

        return [
            'duration' => $duration,
            'options' => $options,
            'isFixedPrice' => $isFixedPrice
        ];
    }
}
