<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_RewardPointsUltimate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\RewardPointsUltimate\Setup;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Downloadable\Model\Product\Type as DownloadableType;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Store\Model\StoreRepository;
use Mageplaza\RewardPointsUltimate\Model\Attribute\Backend\Pattern;
use Mageplaza\RewardPointsUltimate\Model\Config\Source\CustomerGroups;
use Mageplaza\RewardPointsUltimate\Model\MilestoneFactory;

/**
 * Class UpgradeData
 * @package Mageplaza\RewardPointsUltimate\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var MilestoneFactory
     */
    protected $_milestone;

    /**
     * @var Collection
     */
    protected $customerGroup;

    /**
     * @var StoreRepository
     */
    protected $storeRepository;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var $typeBoolean
     */
    private $typeBoolean = Boolean::class;

    /**
     * @var $savePattern
     */
    private $savePattern = Pattern::class;

    /**
     * UpgradeData constructor.
     *
     * @param MilestoneFactory $milestone
     * @param Collection $customerGroup
     * @param StoreRepository $storeRepository
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        MilestoneFactory $milestone,
        Collection $customerGroup,
        StoreRepository $storeRepository,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->_milestone      = $milestone;
        $this->customerGroup   = $customerGroup;
        $this->storeRepository = $storeRepository;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $customerGroup = [];
            $websiteIds    = [];

            foreach ($this->customerGroup->toOptionArray() as $item) {
                if ($item['value'] !== '0') {
                    $customerGroup[] = $item['value'];
                }
            }

            foreach ($this->storeRepository->getList() as $store) {
                $websiteIds[] = $store['website_id'];
            }

            $data = [
                'name'               => __('Base'),
                'status'             => 1,
                'customer_group_ids' => implode(',', $customerGroup),
                'website_ids'        => implode(',', $websiteIds)
            ];
            $post = $this->_milestone->create();
            $post->addData($data)->save();
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->removeAttribute(Product::ENTITY, 'mp_rw_is_active');
            $eavSetup->addAttribute(
                Product::ENTITY,
                'mp_rw_is_active',
                array_merge(
                    $this->getDFOptionsProduct(),
                    [
                        'type'    => 'int',
                        'backend' => '',
                        'label'   => 'Enable',
                        'input'   => 'select',
                        'class'   => 'mp_rw_is_active',
                        'source'  => $this->typeBoolean,
                        'default' => 0
                    ]
                )
            );

            $eavSetup->removeAttribute(Product::ENTITY, 'mp_reward_sell_product');
            $eavSetup->addAttribute(
                Product::ENTITY,
                'mp_reward_sell_product',
                array_merge(
                    $this->getDFOptionsProduct(),
                    [
                        'type'    => 'int',
                        'label'   => 'Reward points',
                        'input'   => 'text',
                        'class'   => '',
                        'source'  => '',
                        'default' => 0
                    ]
                )
            );

            $eavSetup->removeAttribute(Product::ENTITY, 'mp_rw_customer_group');
            $eavSetup->addAttribute(
                Product::ENTITY,
                'mp_rw_customer_group',
                array_merge(
                    $this->getDFOptionsProduct(),
                    [
                        'type'    => 'text',
                        'backend' => $this->savePattern,
                        'label'   => 'Customer Group(s)',
                        'input'   => 'multiselect',
                        'class'   => 'mp_rw_customer_group',
                        'source'  => CustomerGroups::class,
                        'default' => null
                    ]
                )
            );
        }
    }

    /**
     * @return array
     */
    protected function getDFOptionsProduct()
    {
        $productTypes = [
            Type::TYPE_SIMPLE,
            Type::TYPE_VIRTUAL,
            DownloadableType::TYPE_DOWNLOADABLE,
            Configurable::TYPE_CODE
        ];

        return [
            'group'                   => 'Sell Product By Points',
            'global'                  => ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible'                 => true,
            'required'                => false,
            'user_defined'            => false,
            'searchable'              => false,
            'filterable'              => false,
            'comparable'              => false,
            'visible_on_front'        => false,
            'used_in_product_listing' => true,
            'unique'                  => false,
            'frontend'                => '',
            'apply_to'                => join(',', $productTypes)
        ];
    }
}
