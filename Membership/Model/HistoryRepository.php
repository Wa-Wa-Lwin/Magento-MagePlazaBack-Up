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

use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Membership\Api\HistoryRepositoryInterface;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\ResourceModel\History\Collection;
use Mageplaza\Membership\Model\ResourceModel\History\CollectionFactory;

/**
 * Class HistoryRepositoryRepository
 * @package Mageplaza\Membership\Model
 */
class HistoryRepository implements HistoryRepositoryInterface
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
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * @var null
     */
    protected $customerId;

    /**
     * HistoryRepository constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param ItemFactory $itemFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        ItemFactory $itemFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->itemFactory = $itemFactory;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null)
    {
        if ($searchCriteria === null) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }

        $collection = $this->getCollection();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $storeId = $this->storeManager->getStore()->getId();
        foreach ($collection->getItems() as $history) {
            $name = Data::jsonDecode($history->getData('name'));
            $orderItem = $this->itemFactory->create()->load($history->getData('item_id'));
            $history->setItemProductName($orderItem->getName());
            if (!empty($name[$storeId])) {
                $history->setMembershipName($name[$storeId]);
            } elseif (!empty($name[0])) {
                $history->setMembershipName($name[0]);
            } else {
                $history->setMembershipName($history->getData('customer_group_code'));
            }
        }
        /** @var SearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        if ($this->customerId) {
            $collection->addFieldToFilter('customer_id', $this->customerId);
        }

        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function getListByCustomerId($customerId, SearchCriteriaInterface $searchCriteria = null)
    {
        $this->customerId = $customerId;

        return $this->getList($searchCriteria);
    }
}
