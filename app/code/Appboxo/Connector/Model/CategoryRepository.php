<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Appboxo\Connector\Model;
use Appboxo\Connector\Api\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface {
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $_categoryFactory;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productCollectionFactory;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;
    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $_stockItemRepository;
    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface  $productCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     */
    public function __construct(
      \Magento\Store\Model\StoreManagerInterface $storeManager,
      \Magento\Catalog\Model\CategoryFactory $categoryFactory,
      \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
      \Magento\Catalog\Helper\Image $imageHelper,
      \Magento\Framework\Webapi\Rest\Request $request,
      \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
    ) {
       $this->_storeManager = $storeManager;
       $this->_productCollectionFactory = $productCollectionFactory;
       $this->_categoryFactory = $categoryFactory;
       $this->_imageHelper = $imageHelper;
       $this->_request = $request;
       $this->_stockItemRepository = $stockItemRepository;
    }

    public function getAppboxoProducts() {

      $page = ($this->_request->getParam('p'))? $this->_request->getParam('p') : 1;

      $name = ($this->_request->getParam('name'))? $this->_request->getParam('name') : "";

      $style = ($this->_request->getParam('style'))? $this->_request->getParam('style') : "";

      $cat = ($this->_request->getParam('category'))? $this->_request->getParam('category') : 0;

      $pageSize = ($this->_request->getParam('limit'))? $this->_request->getParam('limit') : 12;

      $sorter = ($this->_request->getParam('product_list_order'))? $this->_request->getParam('product_list_order') : "position";

      $sort_action = ($this->_request->getParam('product_list_dir'))? $this->_request->getParam('product_list_dir') : "asc";

      $collection = $this->_productCollectionFactory->create();
      $collection->addAttributeToSelect('*');
      if($cat !== 0){
        $category = $this->_categoryFactory->create()->load($cat);
        $collection->addCategoryFilter($category);
      }
      
      $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
      $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

      if($name != ''){
        $collection->addAttributeToFilter(
        [
         ['attribute' => 'name', 'like' => '%'.$name.'%']
        ]);
      }

      if($style != ''){
        /*$collection->addAttributeToFilter(
        [
         ['attribute' => 'style_bags', 'like' => '%'.$style.'%']
        ]);*/
        $collection->addAttributeToFilter('style_bags',array('finset'=>$style));
        //$collection->addAttributeToFilter('vendor', array('eq' => $bodyStyle));
      }
 
      $collection->setPageSize($pageSize); 
      $collection->setOrder($sorter,$sort_action);
      $collection->setCurPage($page);
      $productData = array();

      foreach ($collection as $product) {
        $productDataArray = $product->getData();
        $productStockInfo = $this->_stockItemRepository->get($productDataArray['entity_id']);
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $productDataArray["currency"] = $currency;
        $productDataArray["qty"] = $productStockInfo->getQty();
        $productDataArray["is_in_stock"] = ($productStockInfo->getIsInStock())?"yes":"no";
        $productDataArray["small_image"] = $this->_imageHelper->init($productDataArray, 'product_page_image_small')->setImageFile($productDataArray['small_image'])->getUrl();
        $productDataArray["image"] = $this->_imageHelper->init($productDataArray, 'product_base_image')->setImageFile($productDataArray['image'])->getUrl();
        $productDataArray["thumbnail"] = $this->_imageHelper->init($productDataArray, 'product_page_image_medium')->setImageFile($productDataArray['thumbnail'])->getUrl();

        if($productDataArray['type_id'] == 'configurable'){
          $child_products = $product->getTypeInstance()->getUsedProducts($product);
          if($child_products){
            foreach ($child_products as $key => $cp) {
              $current_child_product = $cp->getData();
              //$productDataArray['childern'][$key] = $current_child_product;
              $productDataArray['childern'][$key]['id'] = $current_child_product['entity_id'];
              $productDataArray['childern'][$key]['name'] = $current_child_product['name'];
              $productDataArray['childern'][$key]['sku'] = $current_child_product['sku'];
              $productDataArray['childern'][$key]['price'] = $current_child_product['price'];
              $productDataArray['childern'][$key]['special_price'] = (isset($current_child_product['special_price']))?$current_child_product['special_price']:null;
              $productDataArray["childern"][$key]["image"] = $this->_imageHelper->init($cp, 'product_page_image_small')->setImageFile($cp->getImage())->getUrl();
              $productDataArray['childern'][$key]['currency'] = $currency;
            }
          }
        }
        array_push($productData, $productDataArray);
      }
      return $productData;
    }
}