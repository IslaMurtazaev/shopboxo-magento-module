<?php

namespace Appboxo\Connector\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $quoteAddressTable = 'quote';
        $orderTable = 'sales_order';
        $orderGridTable = 'sales_order_grid';

        //Quote address table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'order_paymentid',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 300,
                    'comment' =>'Payment ID',
                    'nullable' => true
                ]
            );
        //Order address table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'order_paymentid',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 300,
                    'comment' =>'Payment ID',
                    'nullable' => true

                ]
            );
            //Order address table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderGridTable),
                'order_paymentid',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 300,
                    'comment' =>'Payment ID',
                    'nullable' => true

                ]
            );

        $setup->endSetup();
    }
}