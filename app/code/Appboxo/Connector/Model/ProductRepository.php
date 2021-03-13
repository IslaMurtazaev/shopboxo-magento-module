<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Appboxo\Connector\Model;
use Appboxo\Connector\Api\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface {
    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;
    /**
     * @param \Magento\Store\Model\App\Emulation              $appEmulation
     * @param \Magento\Store\Model\StoreManagerInterface      $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Image                   $imageHelper
     */
    public function __construct(
       \Magento\Store\Model\App\Emulation $appEmulation,
       \Magento\Store\Model\StoreManagerInterface $storeManager,
       \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
       \Magento\Catalog\Helper\Image $imageHelper
    ) {
       $this->appEmulation = $appEmulation;
       $this->storeManager = $storeManager;
       $this->productRepository = $productRepository;
       $this->imageHelper = $imageHelper;
    }
    public function getProductImageUrl($sku) {
       $storeId = $this->storeManager->getStore()->getId();
       $product = $this->productRepository->get($sku);
       $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
       if (!$product) {
           $response = [
               [
                   "code" => '301',
                   "message" => "SKU " . $productSku . " Not Found On Magento",
               ],
           ];
           return $response;
       } else {
           $image_base_url = $this->imageHelper->init($product, 'product_base_image')->getUrl();
           $image_small_url = $this->imageHelper->init($product, 'product_page_image_small')->getUrl();
           $image_medium_url = $this->imageHelper->init($product, 'product_page_image_medium')->getUrl();
           $image_large_url = $this->imageHelper->init($product, 'product_page_image_large')->getUrl();
           $response = [
               [
                   "product_image_base" => $image_base_url,
                   "product_image_small" => $image_small_url,
                   "product_image_medium" => $image_medium_url,
                   "product_image_full" => $image_large_url
               ],
           ];
           return $response;
       }
       $this->appEmulation->stopEnvironmentEmulation();
    }
}