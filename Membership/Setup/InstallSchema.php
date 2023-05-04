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
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\Membership\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (!$setup->tableExists('mageplaza_membership_list')) {
            $table = $connection->newTable($setup->getTable('mageplaza_membership_list'))
                ->addColumn(
                    'membership_id',
                    Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0']
                )
                ->addColumn('name', Table::TYPE_TEXT, '1M')
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => 0]
                )
                ->addColumn('level', Table::TYPE_INTEGER, null, ['nullable' => false])
                ->addColumn('store_id', Table::TYPE_TEXT, 255, ['nullable' => false])
                ->addColumn('customer_group', Table::TYPE_TEXT, 255, ['nullable' => false])
                ->addColumn(
                    'is_featured',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => 0]
                )
                ->addColumn('featured_label', Table::TYPE_TEXT, 255)
                ->addColumn(
                    'sort_order',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => 0]
                )
                ->addColumn('default_duration_unit', Table::TYPE_TEXT, 255)
                ->addColumn('default_duration_no', Table::TYPE_DECIMAL, '12,4', ['nullable' => false, 'default' => 0])
                ->addColumn('image', Table::TYPE_TEXT, 255)
                ->addColumn('background_color', Table::TYPE_TEXT, 255)
                ->addColumn('default_product', Table::TYPE_TEXT, 255)
                ->addColumn('benefit', Table::TYPE_TEXT, '1M')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['default' => Table::TIMESTAMP_INIT])
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['default' => Table::TIMESTAMP_INIT])
                ->addIndex($setup->getIdxName('mageplaza_membership_list', ['membership_id']), ['membership_id'])
                ->setComment('Mageplaza Membership List');

            $connection->createTable($table);
        }

        if (!$setup->tableExists('mageplaza_membership_customer')) {
            $table = $connection->newTable($setup->getTable('mageplaza_membership_customer'))
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0']
                )
                ->addColumn('last_membership_id', Table::TYPE_INTEGER)
                ->addColumn('inactive_membership_id', Table::TYPE_INTEGER)
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => 0]
                )
                ->addColumn('start_date', Table::TYPE_TIMESTAMP)
                ->addColumn('duration', Table::TYPE_TEXT, 255)
                ->addColumn('membership_price', Table::TYPE_DECIMAL, '12,4', ['nullable' => false, 'default' => 0])
                ->addIndex($setup->getIdxName('mageplaza_membership_customer', ['customer_id']), ['customer_id'])
                ->addForeignKey(
                    $setup->getFkName(
                        'mageplaza_membership_customer',
                        'customer_id',
                        'customer_entity',
                        'entity_id'
                    ),
                    'customer_id',
                    $setup->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Mageplaza Membership Customer');

            $connection->createTable($table);
        }

        if (!$setup->tableExists('mageplaza_membership_history')) {
            $table = $connection->newTable($setup->getTable('mageplaza_membership_history'))
                ->addColumn(
                    'history_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true]
                )
                ->addColumn('item_id', Table::TYPE_INTEGER, null, ['unsigned' => true])
                ->addColumn('customer_id', Table::TYPE_INTEGER, null, ['unsigned' => true])
                ->addColumn('membership_id', Table::TYPE_INTEGER, 10, ['unsigned' => true])
                ->addColumn(
                    'action',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => 0]
                )
                ->addColumn('amount', Table::TYPE_DECIMAL, '12,4', ['nullable' => false, 'default' => 0])
                ->addColumn('created_date', Table::TYPE_TIMESTAMP, null, ['default' => Table::TIMESTAMP_INIT])
                ->addColumn('membership_start', Table::TYPE_TIMESTAMP)
                ->addColumn('membership_data', Table::TYPE_TEXT, '1M')
                ->addIndex(
                    $setup->getIdxName(
                        'mageplaza_membership_history',
                        ['history_id', 'item_id', 'customer_id', 'membership_id']
                    ),
                    ['history_id', 'item_id', 'customer_id', 'membership_id']
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'mageplaza_membership_history',
                        'customer_id',
                        'customer_entity',
                        'entity_id'
                    ),
                    'customer_id',
                    $setup->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'mageplaza_membership_history',
                        'membership_id',
                        'customer_entity',
                        'membership_id'
                    ),
                    'membership_id',
                    $setup->getTable('mageplaza_membership_list'),
                    'membership_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Mageplaza Membership History');

            $connection->createTable($table);
        }

        $setup->endSetup();
    }
}
