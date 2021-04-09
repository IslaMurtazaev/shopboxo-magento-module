<?php

namespace Appboxo\Connector\Plugin\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;

class ProductGet{

    /**
    * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $_storeManager;

    /**
    * @var \Magento\CatalogInventory\Api\StockStateInterface
    */
    protected $_stockItem;

    /**
    * @var \Magento\Catalog\Helper\Image
    */
    protected $_imageHelper;

    /**
    * @param \Magento\CatalogInventory\Api\StockStateInterface $stockItem
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager
    * @param \Magento\Catalog\Helper\Image $imageHelper
    */
    public function __construct(
        \Magento\CatalogInventory\Api\StockStateInterface $stockItem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Image $imageHelper
    )
    {
        $this->_storeManager = $storeManager;
        $this->_stockItem = $stockItem;
        $this->_imageHelper = $imageHelper;
    }

    /**
    * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
    * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $products
    * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function afterGet(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Catalog\Api\Data\ProductInterface $product){

        $imageUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        $extensionattributes = $product->getExtensionAttributes();
        $extensionattributes->setAppboxoimage($imageUrl.'catalog/product');
        $extensionattributes->setAppboxocurrency($currency);
        $product->setExtensionAttributes($extensionattributes);

        return $product;
    }


    /**
    * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
    * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $products
    * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    
    public function afterGetList(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        $products
    ) {
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        foreach ($products->getItems() as $key => $product) {
            $qty = $this->_stockItem->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
            $extensionattributes = $product->getExtensionAttributes();
            $extensionattributes->setQty((int)$qty);
            $extensionattributes->setAppboxocurrency($currency);
            if($product->getImage()){
                $extensionattributes->setAppboxoimage($this->_imageHelper->init($product, 'product_base_image')->setImageFile($product->getImage())->getUrl());
            }
            $product->setExtensionAttributes($extensionattributes);
        }
        return $products;
    }
}