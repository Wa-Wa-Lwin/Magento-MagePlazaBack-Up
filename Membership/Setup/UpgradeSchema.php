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

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Mageplaza\Membership\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $connection->addColumn($setup->getTable('mageplaza_membership_customer'), 'old_membership_id', [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Old Membership Id'
            ]);
            $connection->addColumn($setup->getTable('mageplaza_membership_customer'), 'old_start_date', [
                'type' => Table::TYPE_TIMESTAMP,
                'nullable' => true,
                'comment' => 'Old Start Date'
            ]);
            $connection->addColumn($setup->getTable('mageplaza_membership_customer'), 'old_duration', [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'nullable' => true,
                'comment' => 'Old Duration'
            ]);
        }

        $setup->endSetup();
    }
}
