<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Appboxo\Connector\Model;
use Appboxo\Connector\Api\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface {
  /**
   * @var \Magento\Catalog\Api\ProductRepositoryInterface
   */
  protected $_productRepository;
  /**
   * @var \Magento\Catalog\Helper\Image
   */
  protected $_imageHelper;
  /**
   * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
   * @param \Magento\Catalog\Helper\Image                   $imageHelper
   */
  public function __construct(
     \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
     \Magento\Catalog\Helper\Image $imageHelper
  ) {
     $this->_productRepository = $productRepository;
     $this->_imageHelper = $imageHelper;
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
            //$productDataArray['childern'][$key] = $cp->getData();
            $product['childern'][$key]['id'] = $cp->getId();
            $product['childern'][$key]['sku'] = $cp->getSku();
            $product['childern'][$key]['price'] = $cp->getFinalPrice();
            $product['childern'][$key]['special_price'] = $cp->getSalePrice();
            $product["childern"][$key]["image"] = $this->_imageHelper->init($cp, 'product_page_image_small')->setImageFile($cp->getImage())->getUrl();
           // $productDataArray['childern'][$key]['currency'] = $currency;
          }
        }
      }

      return [$product];
    }
  }
}