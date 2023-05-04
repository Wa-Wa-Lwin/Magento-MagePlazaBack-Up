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

use Exception;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\ResourceModel\Store\Collection;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Membership\Api\Data\MembershipInterface;
use Mageplaza\Membership\Helper\Data;
use Mageplaza\Membership\Model\Config\Source\CustomerStatus;

/**
 * Class Membership
 * @package Mageplaza\Membership\Model
 */
class Membership extends AbstractModel implements MembershipInterface
{
    /**
     * @var null
     */
    protected $currentMemberShip;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /***
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $stores = [];

    /**
     * Membership constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CustomerRegistry $customerRegistry
     * @param StoreManagerInterface $storeManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CustomerRegistry $customerRegistry,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param AbstractModel|int $customer
     *
     * @return Membership
     * @throws NoSuchEntityException
     */
    public function getCurrentMembership($customer)
    {
        if (!$this->currentMemberShip) {
            if (!is_object($customer)) {
                $customer = $this->customerRegistry->retrieve($customer);
            }

            $groupId = (int)$customer->getStatus() !== CustomerStatus::ACTIVE
                ? $customer->getLastMembershipId()
                : $customer->getGroupId();

            $this->currentMemberShip = $this->load($groupId);
        }

        return $this->currentMemberShip;
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\Membership::class);
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     */
    public function attachCustomData(AbstractModel $object)
    {
        $object->addData($this->getData());

        return $this;
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     * @throws Exception
     */
    public function saveCustomData(AbstractModel $object)
    {
        $this->addData($object->getData());

        $this->_resource->save($this);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMembershipId()
    {
        return $this->getData(self::MEMBERSHIP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setMembershipId($value)
    {
        return $this->setData(self::MEMBERSHIP_ID, $value);
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return Collection|array
     */
    public function getStores()
    {
        if (!$this->stores) {
            $stores = $this->storeManager->getStores(true);

            if (is_array($stores)) {
                usort($stores, function ($storeA, $storeB) {
                    /** @var Store $storeA */
                    /** @var Store $storeB */
                    if ($storeA->getSortOrder() === $storeB->getSortOrder()) {
                        return $storeA->getId() < $storeB->getId() ? -1 : 1;
                    }

                    return ($storeA->getSortOrder() < $storeB->getSortOrder()) ? -1 : 1;
                });
            }
            $this->stores = $stores;
        }

        return $this->stores;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @return string
     */
    public function processName()
    {
        $storeLabels = Data::jsonDecode($this->getName());
        $result = [];

        if ($storeLabels) {
            foreach ($this->getStores() as $store) {
                $storeId = $store->getId();
                $result[] = [
                    'store_id' => (int)$storeId,
                    'label' => isset($storeLabels[$storeId]) ? $storeLabels[$storeId] : ''
                ];
            }
        }

        return Data::jsonEncode($result);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return $this->getData(self::LEVEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLevel($value)
    {
        return $this->setData(self::LEVEL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroup()
    {
        return $this->getData(self::CUSTOMER_GROUP . '_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroup($value)
    {
        return $this->setData(self::CUSTOMER_GROUP, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsFeatured()
    {
        return $this->getData(self::IS_FEATURED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsFeatured($value)
    {
        return $this->setData(self::IS_FEATURED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeaturedImage()
    {
        return $this->getData(self::FEATURED_IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFeaturedImage($value)
    {
        return $this->setData(self::FEATURED_IMAGE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeaturedLabel()
    {
        return $this->getData(self::FEATURED_LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setFeaturedLabel($value)
    {
        return $this->setData(self::FEATURED_LABEL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($value)
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultDurationUnit()
    {
        return $this->getData(self::DEFAULT_DURATION_UNIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultDurationUnit($value)
    {
        return $this->setData(self::DEFAULT_DURATION_UNIT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultDurationNo()
    {
        return $this->getData(self::DEFAULT_DURATION_NO);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultDurationNo($value)
    {
        return $this->setData(self::DEFAULT_DURATION_NO, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($value)
    {
        return $this->setData(self::IMAGE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBackgroundColor()
    {
        return $this->getData(self::BACKGROUND_COLOR);
    }

    /**
     * {@inheritdoc}
     */
    public function setBackgroundColor($value)
    {
        return $this->setData(self::BACKGROUND_COLOR, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultProduct()
    {
        return $this->getData(self::DEFAULT_PRODUCT);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultProduct($value)
    {
        return $this->setData(self::DEFAULT_PRODUCT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBenefit()
    {
        return $this->getData(self::BENEFIT);
    }

    /**
     * @return string
     */
    public function processBenefit()
    {
        $benefits = $this->getData(self::BENEFIT);

        $benefits = Data::jsonDecode($benefits);
        $result = [];

        if ($benefits) {
            foreach ($benefits['option']['value'] as $id => $option) {
                foreach ($option as $storeId => $label) {
                    $result[$id][] = [
                        'store_id' => $storeId,
                        'label' => $label
                    ];
                }
            }
        }

        return Data::jsonEncode($result);
    }

    /**
     * {@inheritdoc}
     */
    public function setBenefit($value)
    {
        return $this->setData(self::BENEFIT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($value)
    {
        return $this->setData(self::UPDATED_AT, $value);
    }
}
