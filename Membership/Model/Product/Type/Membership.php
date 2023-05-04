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

namespace Mageplaza\Membership\Model\Product\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\Virtual;
use Magento\Eav\Model\Config;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Option;
use Mageplaza\Membership\Helper\Account;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\Config\Source\DurationType;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\Config\Source\HistoryAction;
use Mageplaza\Membership\Model\Config\Source\MembershipStatus;
use Mageplaza\Membership\Model\Config\Source\UpgradingCost;
use Mageplaza\Membership\Model\Membership as ModelMembership;
use Mageplaza\Membership\Model\MembershipFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;
use Psr\Log\LoggerInterface;
use Zend_Serializer_Exception;

/**
 * Class Membership
 * @package Mageplaza\Membership\Model\Product\Type
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Membership extends Virtual
{
    const TYPE_MEMBERSHIP = 'mpmembership';
    const TYPE_MEMBERSHIP_DATA = 'mpmembership_data';

    /**
     * @var MembershipFactory
     */
    protected $membershipFactory;

    /**
     * @var MembershipResource
     */
    protected $membershipResource;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Account
     */
    protected $accountHelper;

    /**
     * Membership constructor.
     *
     * @param Product\Option $catalogProductOption
     * @param Config $eavConfig
     * @param Product\Type $catalogProductType
     * @param ManagerInterface $eventManager
     * @param Database $fileStorageDb
     * @param Filesystem $filesystem
     * @param Registry $coreRegistry
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param MembershipFactory $membershipFactory
     * @param MembershipResource $membershipResource
     * @param Data $helper
     * @param Account $accountHelper
     */
    public function __construct(
        Product\Option $catalogProductOption,
        Config $eavConfig,
        Product\Type $catalogProductType,
        ManagerInterface $eventManager,
        Database $fileStorageDb,
        Filesystem $filesystem,
        Registry $coreRegistry,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        MembershipFactory $membershipFactory,
        MembershipResource $membershipResource,
        Data $helper,
        Account $accountHelper
    ) {
        $this->membershipFactory = $membershipFactory;
        $this->membershipResource = $membershipResource;
        $this->helper = $helper;
        $this->accountHelper = $accountHelper;

        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );
    }

    /**
     * @param Product $product
     *
     * @return $this
     */
    public function beforeSave($product)
    {
        parent::beforeSave($product);

        $product->setTypeHasOptions(true);

        return $this;
    }

    /**
     * @param Product $product
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function canConfigure($product)
    {
        /** @var Product $productClone */
        $productClone = $this->productRepository->getById($product->getId());

        return $productClone->getData(FieldRenderer::DURATION) === DurationType::CUSTOM;
    }

    /**
     * Check if product is available for sale
     *
     * @param Product $product
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isSalable($product)
    {
        /** @var Product $productClone */
        $productClone = $this->productRepository->getById($product->getId());

        $membership = $this->membershipFactory->create();
        $this->membershipResource->load($membership, $productClone->getData(FieldRenderer::MEMBERSHIP));

        if (!$this->helper->isEnabled() || (int)$membership->getStatus() !== MembershipStatus::ACTIVE) {
            return false;
        }

        return parent::isSalable($product);
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     * @param string $processMode
     *
     * @return array|Phrase|string
     * @throws LocalizedException
     */
    protected function _prepareProduct(DataObject $buyRequest, $product, $processMode)
    {

        $result = parent::_prepareProduct($buyRequest, $product, $processMode);

        if (is_string($result)) {
            return $result;
        }

        $quote = $this->helper->getCheckoutSession()->getQuote();
        if (!$quote->getId() && $buyRequest->getQuoteId()) {
            $quote->loadActive($buyRequest->getQuoteId());
        }

        $qtyRequest = $buyRequest->getData('qty');
        $toCheck = false;

        foreach (array_keys($buyRequest->getData()) as $key) {
            if (strpos($key, 'mpmembership') !== false) {
                $toCheck = true;
            }
        }

        if ($qtyRequest || $toCheck) {
            if ($customer = $this->accountHelper->getCurrentCustomer()) {
                $storeId = $quote->getStoreId();
                $errMsg = $this->checkCurrentCustomerCanBuy($product, $storeId);
                if ($errMsg) {
                    return __($errMsg)->render();
                }
            }

            if (!$buyRequest->getData('reset_count')) {
                /** @var Item $item */
                foreach ($quote->getAllItems() as $item) {
                    if ($item->getProductType() === self::TYPE_MEMBERSHIP) {
                        return __('Your cart already contains a membership product.')->render();
                    }
                }
            }
        }

        if ($qtyRequest > 1) {
            $buyRequest->setData('qty', 1);
        }

        return $this->_prepareMembershipData($buyRequest, $product);
    }

    /**
     * Check if product can be bought
     *
     * @param Product $product
     *
     * @return $this
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    public function checkProductBuyState($product)
    {
        parent::checkProductBuyState($product);

        $option = $product->getCustomOption('info_buyRequest');
        if ($option instanceof Option) {
            $buyRequest = new DataObject($this->helper->unserialize($option->getValue()));

            $this->_prepareMembershipData($buyRequest, $product);
        }

        return $this;
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     *
     * @return array|string
     * @throws NoSuchEntityException
     */
    protected function _prepareMembershipData($buyRequest, $product)
    {
        /** @var Product $productClone */
        $productClone = $this->productRepository->getById($product->getId());

        $data = $this->_processMembershipData($buyRequest, $productClone);

        if (is_string($data)) {
            return $data;
        }

        [$membership, $duration, $price] = $data;

        $product->addCustomOption(FieldRenderer::ACTION, $buyRequest->getData(FieldRenderer::ACTION), $product);
        $product->addCustomOption(FieldRenderer::MEMBERSHIP, $membership, $product);
        $product->addCustomOption(FieldRenderer::DURATION, $duration, $product);
        $product->addCustomOption(FieldRenderer::PRICE, $price, $product);

        return [$product];
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     *
     * @return array|string
     */
    protected function _processMembershipData($buyRequest, $product)
    {
        $membershipId = $product->getData(FieldRenderer::MEMBERSHIP);

        $price = $product->getData(FieldRenderer::PRICE);

        $duration = [];
        switch ($product->getData(FieldRenderer::DURATION)) {
            case DurationType::PERMANENT:
                $duration = ['permanent' => __('Permanent')];

                break;
            case DurationType::CUSTOM:
                $options = $product->getData(FieldRenderer::OPTIONS) ?: [];
                foreach ($options as $option) {
                    if ($option['record_id'] === $buyRequest->getData(self::TYPE_MEMBERSHIP_DATA)) {
                        $duration = ['number' => $option['number'], 'unit' => $option['unit']];
                        $price = $option['price'];
                        break;
                    }
                }

                break;
            default:
                $membership = $this->membershipFactory->create();
                $this->membershipResource->load($membership, $product->getData(FieldRenderer::MEMBERSHIP));
                $duration = [
                    'unit' => $membership->getDefaultDurationUnit(),
                    'number' => $membership->getDefaultDurationNo()
                ];
        }

        if (empty($duration) && $buyRequest->getData('qty')) {
            return __('Duration is incorrect. Please choose your duration again')->render();
        }

        if ((int)$buyRequest->getData(FieldRenderer::ACTION) === HistoryAction::UPGRADE) {
            $price -= $this->getDeductAmount();
        }

        return [$membershipId, Data::jsonEncode($duration), $price];
    }

    /**
     * @param Product $product
     * @param DataObject $buyRequest
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function processBuyRequest($product, $buyRequest)
    {
        /** @var Product $productClone */
        $productClone = $this->productRepository->getById($product->getId());

        [$membership, $duration, $price] = $this->_processMembershipData($buyRequest, $productClone);

        $options = [
            FieldRenderer::ACTION => $buyRequest->getData(FieldRenderer::ACTION),
            FieldRenderer::MEMBERSHIP => $membership,
            FieldRenderer::DURATION => $duration,
            FieldRenderer::PRICE => $price
        ];

        return $options;
    }

    /**
     * @return float
     */
    protected function getDeductAmount()
    {
        $customer = $this->accountHelper->getCurrentCustomer();

        if (!$customer
            || $this->helper->getUpgradingCost() !== UpgradingCost::DEDUCT_REMAIN
            || (int)$customer->getStatus() !== CustomerStatus::ACTIVE
            || $customer->getDuration() === null
        ) {
            return 0;
        }

        $rate = 1;

        if ($duration = $customer->getDuration()) {
            $remaining = strtotime($customer->getStartDate()) + $duration - time();
            $remaining -= $remaining % 3600;

            $rate = $remaining / $duration;
        }

        return $customer->getMembershipPrice() * $rate;
    }

    /**
     * get customre status
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
     * check customer with current membership can upgrade or not
     * @return bool
     */
    public function canUpgrade() {
        if ($this->getCustomerStatus() !== CustomerStatus::ACTIVE || !$this->helper->isAllowUpgrade()) {
            return false;
        }

        return true;
    }

    /**
     * get current membership by customer
     * @return ModelMembership
     */
    public function getCurrentMembership() {
        return $this->membershipFactory->create()->getCurrentMembership($this->accountHelper->getCurrentCustomer());
    }

    /**
     * check current customer buy current membership product or not
     * @param Product $product
     * @param int $storeId
     */
    public function checkCurrentCustomerCanBuy($product, $storeId) {

        if ($this->helper->isAllowUpgrade($storeId)) {
            $customerMemberShip = $this->getCurrentMembership();
            $customer = $this->accountHelper->getCurrentCustomer();
            if ($customerMemberShip && $customer->getStatus() == CustomerStatus::ACTIVE) {
                $curLevel = $customerMemberShip->getLevel();
                $customerMemberShip->getStatus();

                if ($this->getMembershipByProductId($product->getId()) > 0) {
                    if($curLevel >= $this->getMembershipByProductId($product->getId()) && $curLevel >= $this->currentProductMemberShipLevel($product)) {
                        return __('Cannot buy same membership before expiration.');
                    }
                } else {
                    if (!$this->helper->isAllowOverride($storeId) && $this->getMembershipByProductId($product->getId()) == 0 && $curLevel != $this->currentProductMemberShipLevel($product)) {
                        return __('Cannot buy others membership products.');
                    }
                }
                //return $curLevel < $this->getMembershipByProductId($product->getId()) || $curLevel == $this->currentProductMemberShipLevel($product);
            }
            return;
        } else {
            $customerMemberShip = $this->getCurrentMembership();
            $customer = $this->accountHelper->getCurrentCustomer();
            $curLevel = $customerMemberShip->getLevel();
            if ($customer->getStatus() == CustomerStatus::ACTIVE) {
                if ($this->getMembershipByProductId($product->getId()) > 0) {
                    return __('Cannot upgrade membership before expiration.')->render();
                } else {
                    if (!$this->helper->isAllowOverride($storeId) && $this->getMembershipByProductId($product->getId()) == 0 && $curLevel != $this->currentProductMemberShipLevel($product)) {
                        return __('Cannot buy others membership products.');
                    }
                }
            } else {
                if (!$this->helper->isAllowOverride($storeId) && $this->getMembershipByProductId($product->getId()) == 0 && $curLevel != $this->currentProductMemberShipLevel($product)) {
                    return __('Cannot buy others membership products.');
                }
            }
        }
        return;
    }
    
    public function getMembershipByProductId($id) {
        $collection = $this->membershipFactory->create()->getCollection()->addFieldToFilter('default_product', $id);
        if ($collection->getSize()) {
            return $collection->getFirstItem()->getLevel();
        }
        return 0;
    }

    public function currentProductMemberShipLevel($product) {
        $collection = $this->membershipFactory->create()->load($product->getOrigData(FieldRenderer::MEMBERSHIP));
        return $collection->getLevel();

    }

    


}

