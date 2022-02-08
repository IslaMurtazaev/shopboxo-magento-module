<?php

namespace Appboxo\Connector\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\ResourceModel\Store;

class UpgradeSchema implements UpgradeSchemaInterface
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
     * UpgradeSchema constructor.
     * @param StoreFactory $storeFactory
     * @param StoreManagerInterface $storeManager
     * @param ManagerInterface $eventManager
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