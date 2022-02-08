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
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\ResourceModel\Store;

class InstallData implements InstallDataInterface
{
    /**
     * @var StoreFactory
     */
    protected $_storeFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var ManagerInterface
     */
    protected $_eventManager;
    /**
     * @var Store
     */
    protected $_storeResourceModel;

    /**
     * InstallData constructor.
     * @param StoreFactory $storeFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreFactory $storeFactory,
        StoreManagerInterface $storeManager,
        ManagerInterface $eventManager,
        Store $storeResourceModel
    ) {
        $this->_storeFactory = $storeFactory;
        $this->_storeManager = $storeManager;
        $this->_eventManager = $eventManager;
        $this->_storeResourceModel = $storeResourceModel;
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

        $store = $this->_storeFactory->create();
        $store->load('mp_storeview');
        if (!$store->getId()) {
            $store->setCode('mp_storeview');
            $store->setName(' MP Store View');
            $store->setWebsiteId($this->_storeManager->getDefaultStoreView()->getWebsiteId());
            $store->setGroupId($this->_storeManager->getDefaultStoreView()->getGroupId());
            $store->setData('is_active', '1');
            $store->setSortOrder(0);
            try {
                $this->_storeResourceModel->save($store);
            } catch (\Exception $e) {
                echo $e->getMessage();                    
                if (!$store->getId()) {
                    return;
                }
            }
            $this->_eventManager->dispatch('store_add', ['store' => $store]);
        }

        $setup->endSetup();
    }
}
