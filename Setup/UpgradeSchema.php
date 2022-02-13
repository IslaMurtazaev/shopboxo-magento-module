<?php

namespace Appboxo\Connector\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Store\Model\StoreFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\ResourceModel\Store;
use Magento\Store\Model\WebsiteFactory;
use Magento\Store\Model\ResourceModel\Website;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var WebsiteFactory
     */
    protected $_websiteFactory;
    /**
     * @var GroupFactory
     */
    protected $_groupFactory;
    /**
     * @var Group
     */
    protected $_groupResourceModel;
    /**
     * @var StoreFactory
     */
    protected $_storeFactory;
    /**
     * @var ManagerInterface
     */
    protected $_eventManager;
     /**
     * @var Website
     */
    protected $_websiteResourceModel;
    /**
     * @var Store
     */
    protected $_storeResourceModel;
    /**
     * UpgradeSchema constructor.
     * @param WebsiteFactory $WebsiteFactory
     * @param Website $websiteResourceModel
     * @param StoreFactory $StoreFactory
     * @param ManagerInterface $eventManager
     * @param GroupFactory $GroupFactory
     * @param Group $groupResourceModel
     * @param Store $storeResourceModel
     */
    public function __construct(
        WebsiteFactory $websiteFactory,
        Website $websiteResourceModel,
        StoreFactory $storeFactory,
        ManagerInterface $eventManager,
        GroupFactory $groupFactory,
        Group $groupResourceModel,
        Store $storeResourceModel
    ) {
        $this->_websiteFactory = $websiteFactory;
        $this->_websiteResourceModel = $websiteResourceModel;
        $this->_storeFactory = $storeFactory;
        $this->_eventManager = $eventManager;
        $this->_storeResourceModel = $storeResourceModel;
        $this->_groupFactory = $groupFactory;
        $this->_groupResourceModel = $groupResourceModel;
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

        /** @var \Magento\Store\Model\Website $website */
        $website = $this->_websiteFactory->create();
        $website->load('mp_web');
        if(!$website->getId()){
            $website->setCode('mp_web');
            $website->setName('MP Website');
            $this->_websiteResourceModel->save($website);
        }

        if($website->getId()){
            /** @var \Magento\Store\Model\Group $group */
            $group = $this->_groupFactory->create();
            $group->setWebsiteId($website->getWebsiteId());
            $group->setName('MP Store');
            $group->setCode('mp_store');
            $this->_groupResourceModel->save($group);
        }

        $store = $this->_storeFactory->create();
        $store->load('mp_storeview');
        if (!$store->getId()) {
            $store->setCode('mp_storeview');
            $store->setName('MP Store View');
            $store->setWebsiteId($website->getWebsiteId());
            $store->setGroupId($group->getGroupId());
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