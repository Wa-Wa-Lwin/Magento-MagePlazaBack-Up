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

namespace Mageplaza\Membership\Setup;

use Exception;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Backend\Price;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Mageplaza\Membership\Model\Attribute\Backend\DurationOptions;
use Mageplaza\Membership\Model\Config\Source\DurationType;
use Mageplaza\Membership\Model\Config\Source\FieldRenderer;
use Mageplaza\Membership\Model\Config\Source\MembershipOptions;
use Mageplaza\Membership\Model\Product\Type\Membership;
use Psr\Log\LoggerInterface;
use Zend_Validate_Exception;

/**
 * Class InstallData
 * @package Mageplaza\Membership\Setup
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var Product\AttributeSet\Options
     */
    protected $attributeSet;

    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var File
     */
    protected $file;

    /**
     * InstallData constructor.
     *
     * @param Product\AttributeSet\Options $attributeSet
     * @param CategorySetupFactory $categorySetupFactory
     * @param LoggerInterface $logger
     * @param File $file
     */
    public function __construct(
        Product\AttributeSet\Options $attributeSet,
        CategorySetupFactory $categorySetupFactory,
        LoggerInterface $logger,
        File $file
    ) {
        $this->attributeSet = $attributeSet;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->logger = $logger;
        $this->file = $file;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var CategorySetup $catalogSetup */
        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);

        /** Create product attribute group */
        $entityTypeId = $catalogSetup->getEntityTypeId(Category::ENTITY);

        foreach ($this->attributeSet->toOptionArray() as $set) {
            $catalogSetup->addAttributeGroup($entityTypeId, $set['value'], 'Membership Information', 10);
        }

        /** Add Product Attribute */
        $catalogSetup->addAttribute(
            Product::ENTITY,
            FieldRenderer::MEMBERSHIP,
            array_merge($this->getDefaultOptions(), [
                'label' => 'Membership',
                'type' => 'int',
                'input' => 'select',
                'source' => MembershipOptions::class,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'sort_order' => 10
            ])
        );
        $catalogSetup->addAttribute(Product::ENTITY, FieldRenderer::DURATION, array_merge($this->getDefaultOptions(), [
            'label' => 'Duration',
            'type' => 'text',
            'input' => 'select',
            'source' => DurationType::class,
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 20
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, FieldRenderer::PRICE, array_merge($this->getDefaultOptions(), [
            'label' => 'Price',
            'type' => 'decimal',
            'input' => 'price',
            'backend' => Price::class,
            'class' => 'validate-number',
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 30
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, FieldRenderer::OPTIONS, array_merge($this->getDefaultOptions(), [
            'label' => 'Duration Options',
            'type' => 'text',
            'input' => 'text',
            'backend' => DurationOptions::class,
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 40
        ]));

        $this->updateApplyTo($catalogSetup);
        $this->copyIconDefault();
    }

    /**
     * Update apply_to for store credit product attribute
     *
     * @param CategorySetup $catalogSetup
     *
     * @return $this
     */
    protected function updateApplyTo($catalogSetup)
    {
        $fieldAdd = ['tax_class_id'];
        foreach ($fieldAdd as $field) {
            $applyTo = $catalogSetup->getAttribute('catalog_product', $field, 'apply_to');
            if ($applyTo) {
                $applyTo = explode(',', $applyTo);
                if (!in_array(Membership::TYPE_MEMBERSHIP, $applyTo, true)) {
                    $applyTo[] = Membership::TYPE_MEMBERSHIP;
                    $catalogSetup->updateAttribute('catalog_product', 'weight', 'apply_to', implode(',', $applyTo));
                }
            }
        }

        $fieldRemove = ['cost'];
        foreach ($fieldRemove as $field) {
            $applyTo = explode(',', $catalogSetup->getAttribute('catalog_product', $field, 'apply_to'));
            if (in_array(Membership::TYPE_MEMBERSHIP, $applyTo, true)) {
                foreach ($applyTo as $k => $v) {
                    if ($v === Membership::TYPE_MEMBERSHIP) {
                        unset($applyTo[$k]);
                        break;
                    }
                }
                $catalogSetup->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'group' => 'Membership Information',
            'backend' => '',
            'frontend' => '',
            'class' => '',
            'source' => '',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'unique' => false,
            'apply_to' => Membership::TYPE_MEMBERSHIP,
            'used_in_product_listing' => true
        ];
    }

    /**
     * Copy icon default to media path
     */
    protected function copyIconDefault()
    {
        try {
            /** @var Filesystem\Directory\WriteInterface $mediaDirectory */
            $mediaDirectory = ObjectManager::getInstance()->get(Filesystem::class)
                ->getDirectoryWrite(DirectoryList::MEDIA);

            $mediaDirectory->create('mageplaza/membership/default');
            $targetPath = $mediaDirectory->getAbsolutePath('mageplaza/membership/default/featured.png');

            $DS = DIRECTORY_SEPARATOR;
            $oriPath = $this->file->dirname(__DIR__) . $DS . 'view' . $DS . 'frontend' . $DS . 'web' . $DS .
                'images' . $DS . 'default' . $DS . 'featured.png';

            $mediaDirectory->getDriver()->copy($oriPath, $targetPath);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
