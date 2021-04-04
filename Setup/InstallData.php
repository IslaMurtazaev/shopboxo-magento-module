<?php
/**
 * Copyright © Appboxo. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Appboxo\Connector\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;

class InstallData implements InstallDataInterface
{
    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;
    
    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * InstallData constructor.
     * @param SalesSetupFactory $salesSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        $quoteAddressTable = 'quote';
        $orderTable = 'sales_order';
        $orderGridTable = 'sales_order_grid';

        //Quote address table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'ac_payment_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' =>'Payment ID',
                    'nullable' => true
                ]
            );
        //Order address table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'ac_payment_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' =>'Payment ID',
                    'nullable' => true

                ]
            );
            //Order address table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderGridTable),
                'ac_payment_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' =>'Payment ID',
                    'nullable' => true

                ]
            );


        /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
        /*$quoteInstaller = $this->quoteSetupFactory->create(
            ['resourceName' => 'quote_setup', 'setup' => $setup]
        );*/

        /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
        /*$salesInstaller = $this->salesSetupFactory->create(
            ['resourceName' => 'sales_setup', 'setup' => $setup]
        );
        
        $quoteInstaller->addAttribute(
            'quote',
            'order_paymentid',
            [
              'type' => Table::TYPE_TEXT,
              'length' => 300, 'nullable' => true
            ]
        );

        $salesInstaller->addAttribute(
            'order',
            'order_paymentid',
            [
              'type' => Table::TYPE_TEXT,
              'length' => 300, 'nullable' => true,
              'grid' => true
            ]
        );*/

        $setup->endSetup();
    }
}
