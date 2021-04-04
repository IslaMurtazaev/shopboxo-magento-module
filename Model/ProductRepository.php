<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Appboxo\Connector\Model;
use Appboxo\Connector\Api\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface {
  /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
  /**
   * @var \Magento\Catalog\Api\ProductRepositoryInterface
   */
  protected $_productRepository;
  /**
   * @var \Magento\Catalog\Helper\Image
   */
  protected $_imageHelper;
  /**
   * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
   */
  protected $_stockItemRepository;
  /**
   * @param \Magento\Store\Model\StoreManagerInterface $storeManager
   * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
   * @param \Magento\Catalog\Helper\Image                   $imageHelper
   * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
   */
  public function __construct(
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
    \Magento\Catalog\Helper\Image $imageHelper,
    \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
  ) {
    $this->_storeManager = $storeManager;
     $this->_productRepository = $productRepository;
     $this->_imageHelper = $imageHelper;
     $this->_stockItemRepository = $stockItemRepository;
  }

  public function getAppboxoProduct($sku) {
    $current_product = $this->_productRepository->get($sku);

    if (!$current_product) {
       $response = [
           [
               "code" => '301',
               "message" => "SKU " . $productSku . " Not Found On Magento",
           ],
       ];
       return $response;
    } else {
      $product = $current_product->getData();
      $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
      $product["small_image"] = $this->_imageHelper->init($product, 'product_page_image_small')->setImageFile($product['small_image'])->getUrl();
      $product["image"] = $this->_imageHelper->init($product, 'product_base_image')->setImageFile($product['image'])->getUrl();
      $product["thumbnail"] = $this->_imageHelper->init($product, 'product_page_image_medium')->setImageFile($product['thumbnail'])->getUrl();

      if(count($product['media_gallery']['images']) > 0 ){
        foreach ($product['media_gallery']['images'] as $key => $img) {
          $product['media_gallery']['images'][$key]['file'] = $this->_imageHelper->init($product, 'product_base_image')->setImageFile($img['file'])->getUrl();
        }
      }
      if($product['type_id'] == 'configurable'){
        $child_products = $current_product->getTypeInstance()->getUsedProducts($current_product);
        if($child_products){
          foreach ($child_products as $key => $cp) {
            $product['childern'][$key] = $cp->getData();
            $productStockInfo = $this->_stockItemRepository->get($product['childern'][$key]['entity_id']);
            $product['childern'][$key]["qty"] = $productStockInfo->getQty();
            $product['childern'][$key]["is_in_stock"] = ($productStockInfo->getIsInStock())?"yes":"no";
            $product["childern"][$key]["image"] = $this->_imageHelper->init($cp, 'product_page_image_small')->setImageFile($cp->getImage())->getUrl();
            $product["childern"][$key]["small_image"] = "";
            $product["childern"][$key]["thumbnail"] = "";
            $product["childern"][$key]["media_gallery"] = [];

        
            $productDataArray['childern'][$key]['currency'] = $currency;
          }
        }
      }

      return [$product];
    }
  }
}