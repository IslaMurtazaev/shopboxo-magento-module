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
    * @var \Magento\Catalog\Api\ProductRepositoryInterface
    */
    protected $productRepository;

    /**
    * @var \Magento\Catalog\Api\Data\ProductInterfaceFactory
    */
    protected $productFactory;

    /**
    * @var \Magento\Framework\Api\DataObjectHelper
    */
    protected $dataObjectHelper;
    /**
    * @param \Magento\CatalogInventory\Api\StockStateInterface $stockItem
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager
    * @param \Magento\Catalog\Helper\Image $imageHelper
    */
    public function __construct(
        \Magento\CatalogInventory\Api\StockStateInterface $stockItem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    )
    {
        $this->_storeManager = $storeManager;
        $this->_stockItem = $stockItem;
        $this->_imageHelper = $imageHelper;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->dataObjectHelper = $dataObjectHelper;
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
        $price = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        $extensionattributes->setAppboxoprice($price);
        $extensionattributes->setAppboxocurrency($currency);
        $extensionattributes->setAppboxoimage($imageUrl.'catalog/product');
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
            $price = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
            $extensionattributes->setAppboxoprice($price);
            $extensionattributes->setAppboxocurrency($currency);
            if($product->getImage()){
                $extensionattributes->setAppboxoimage($this->_imageHelper->init($product, 'product_thumbnail_image')->setImageFile($product->getImage())->getUrl());
            }
            $product->setExtensionAttributes($extensionattributes);
        }
        return $products;
    }

    public function getChildren($sku)
    {
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $product = $this->productRepository->get($sku);
        if ($product->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return [];
        }

        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter($product->getStoreId(), $product);

        $childrenList = [];

        foreach ($productTypeInstance->getUsedProducts($product) as $child) {
            $attributes = [];
            foreach ($child->getAttributes() as $attribute) {
                $attrCode = $attribute->getAttributeCode();
                $value = $child->getDataUsingMethod($attrCode) ?: $child->getData($attrCode);
                if (null !== $value) {
                    $attributes[$attrCode] = $value;
                }
            }
            $attributes['store_id'] = $child->getStoreId();

            $productDataObject = $this->productFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $productDataObject,
                $attributes,
                \Magento\Catalog\Api\Data\ProductInterface::class
            );

            $qty = $this->_stockItem->getStockQty($productDataObject->getId(), $productDataObject->getStore()->getWebsiteId());


            $extensionattributes = $productDataObject->getExtensionAttributes();

            $extensionattributes->setQty((int)$qty);
            
            $price = $productDataObject->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
            $extensionattributes->setAppboxoprice($price);
            $extensionattributes->setAppboxocurrency($currency);
            if($product->getImage()){
                $extensionattributes->setAppboxoimage($this->_imageHelper->init($productDataObject, 'product_thumbnail_image')->setImageFile($productDataObject->getImage())->getUrl());
            }
            $productDataObject->setExtensionAttributes($extensionattributes);

            $childrenList[] = $productDataObject;
        }

        return $childrenList;
    }
}