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

namespace Mageplaza\Membership\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Membership\Api\MembershipRepositoryInterface;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;
use Mageplaza\Membership\Model\Config\Source\DateUnit;
use Mageplaza\Membership\Model\Config\Source\DurationType;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\Config\Source\UpgradingCost;
use Mageplaza\Membership\Model\ResourceModel\Membership as MembershipResource;
use Mageplaza\Membership\Model\ResourceModel\Membership\Api\MembershipPageCollectionFactory;
use Mageplaza\Membership\Model\ResourceModel\Membership\Collection;
use Mageplaza\Membership\Model\ResourceModel\Membership\CollectionFactory;

/**
 * Class MembershipRepository
 * @package Mageplaza\Membership\Model
 */
class MembershipRepository implements MembershipRepositoryInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var MembershipFactory
     */
    protected $membershipFactory;

    /**
     * @var MembershipPageCollectionFactory
     */
    protected $membershipPageCollectionFactory;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var MembershipResource
     */
    protected $membershipResource;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /***
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * MembershipRepository constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param MembershipFactory $membershipFactory
     * @param MembershipPageCollectionFactory $membershipPageCollectionFactory
     * @param CustomerRegistry $customerRegistry
     * @param MembershipResource $membershipResource
     * @param Data $helperData
     * @param StoreManagerInterface $storeManager
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MembershipFactory $membershipFactory,
        MembershipPageCollectionFactory $membershipPageCollectionFactory,
        CustomerRegistry $customerRegistry,
        MembershipResource $membershipResource,
        Data $helperData,
        StoreManagerInterface $storeManager,
        ProductRepository $productRepository
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->membershipFactory = $membershipFactory;
        $this->membershipResource = $membershipResource;
        $this->membershipPageCollectionFactory = $membershipPageCollectionFactory;
        $this->customerRegistry = $customerRegistry;
        $this->helperData = $helperData;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null)
    {
        if (!$this->helperData->isEnabled()) {
            throw new LocalizedException(__('The module is disabled'));
        }

        if ($searchCriteria === null) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('main_table.membership_id', ['notnull' => true]);
        $this->collectionProcessor->process($searchCriteria, $collection);
        foreach ($collection->getItems() as $item) {
            $item->processName();
            $item->processBenefit();
        }

        /** @var SearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function getMembershipPage(SearchCriteriaInterface $searchCriteria = null)
    {
        if (!$this->helperData->isEnabled()) {
            throw new LocalizedException(__('The module is disabled'));
        }

        return $this->getUpgradePage(0, $searchCriteria);
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getUpgradePage($customerId, SearchCriteriaInterface $searchCriteria = null)
    {
        if (!$this->helperData->isEnabled()) {
            throw new LocalizedException(__('The module is disabled'));
        }

        /** @var SearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();

        /** @var Collection $collection */
        $collection = $this->membershipPageCollectionFactory->create();
        $deductAmount = 0;
        $storeId = $this->storeManager->getStore()->getId();
        if ($customerId) {
            $customer = $this->customerRegistry->retrieve($customerId);
            if ((int)$customer->getStatus() !== CustomerStatus::ACTIVE) {
                return $searchResult;
            }

            $membership = $this->membershipFactory->create();
            $this->membershipResource->load($membership, $customer->getGroupId());
            $collection->addFieldToFilter('level', ['gt' => $membership->getLevel()]);
            $deductAmount = $this->getDeductAmount($customer);
        }

        if ($searchCriteria === null) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }

        $this->collectionProcessor->process($searchCriteria, $collection);

        foreach ($collection->getItems() as $item) {
            /**
             * @var Membership $item
             */
            $item->setName($this->processNameByStoreId($storeId, $item));
            $item->setCustomerGroup($item->getCustomerGroup());
            if ($image = $item->getImage()) {
                $item->setImage($this->helperData->getMediaUrl($image, false));
            }
            $item->setFeaturedImage($this->helperData->getFeaturedImageUrl());
            $this->processBenefitByStoreId($storeId, $item);
            if (!$item->getBackgroundColor()) {
                $item->setBackgroundColor('#1979c3');
            }

            $product = $this->productRepository->getById($item->getDefaultProduct());
            $price = 0;
            $price = max($price, $product->getData(FieldRenderer::PRICE) - $deductAmount);

            if ($price) {
                $item->setPrice($this->helperData->convertPrice($price, true, false));
            }
            $this->processOptions($product, $deductAmount, $item);

            $item->setIsOutOfStock(!$product || !$product->isAvailable());
            $this->processDuration($item, $product);
        }

        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * {@inheritdoc}
     */
    public function processOptions($product, $deductAmount, $membership)
    {
        $duration = $product->getData(FieldRenderer::DURATION);
        $options = [];
        if ($duration === DurationType::CUSTOM) {
            $options = $product->getData(FieldRenderer::OPTIONS);
        }

        foreach ($options as $key => $option) {
            $price = max(0, $option['price'] - $deductAmount);
            $price = $this->helperData->convertPrice($price, true, false);

            $options[$key]['membership'] = $membership->getId();
            $options[$key]['formattedPrice'] = $price;

            $options[$key]['label'] = $price . ' - ' . $this->helperData->getDurationText(Data::jsonEncode($option));
        }

        if ($options) {
            $membership->setOptions(Data::jsonEncode($options));
        }
    }

    /**
     * @param DataObject $membership
     * @param ProductInterface $product
     *
     * @return mixed
     */
    public function processDuration($membership, $product)
    {
        $durationUnit = DateUnit::getUnitStatic($membership->getDefaultDurationUnit());
        $durationNo = (float)$membership->getDefaultDurationNo();
        $duration = $durationNo ? $durationNo . ' ' . $durationUnit : __('Permanent');
        switch ($product->getData(FieldRenderer::DURATION)) {
            case DurationType::PERMANENT:
                $duration = __('Permanent');
                break;
            case DurationType::CUSTOM:
                $duration = null;
                break;
            default:
        }

        return $membership->setDuration($duration);
    }

    /**
     * @param Customer $customer
     *
     * @return float|int
     */
    public function getDeductAmount($customer)
    {
        if (!$customer
            || $this->helperData->getUpgradingCost() !== UpgradingCost::DEDUCT_REMAIN
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
     * @inheritDoc
     *
     * @return Membership
     * @throws NoSuchEntityException
     */
    public function getCurrentMembership($customerId)
    {
        $membership = $this->membershipFactory->create()->getCurrentMembership($customerId);
        if (!$membership->getId()) {
            throw new NoSuchEntityException(__('Membership doesn\'t exist'));
        }

        $storeId = $this->storeManager->getStore()->getId();
        $membership->setName($this->processNameByStoreId($storeId, $membership));
        $membership->setFeaturedImage($this->helperData->getFeaturedImageUrl());
        if ($image = $membership->getImage()) {
            $membership->setImage($this->helperData->getMediaUrl($image, false));
        }

        $this->processBenefitByStoreId($storeId, $membership);

        return $membership;
    }

    /**
     * @param int $storeId
     * @param Membership $membership
     *
     * @return mixed
     */
    public function processNameByStoreId($storeId, $membership)
    {
        $storeLabels = Data::jsonDecode($membership->getData('name'));

        return isset($storeLabels[$storeId]) && $storeLabels[$storeId] ?
            $storeLabels[$storeId] : $storeLabels[0];
    }

    /**
     * @param int $storeId
     * @param Membership $membership
     *
     * @return array|string
     */
    public function processBenefitByStoreId($storeId, $membership)
    {
        $benefits = $membership->getBenefit();
        $value = Data::jsonDecode($benefits);

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
        if ($result) {
            $membership->setBenefit(Data::jsonEncode($result));
        }

        return $result;
    }
}
