<?php

namespace Appboxo\Connector\Plugin\Model\Cart;

class TotalRepositoryPlugin
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    protected $itemFactory;
    /**
     *@var \Magento\Catalog\Helper\ImageFactory
     */
    protected $productImageHelper;

    protected $totalItemExtension;
    /**
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param \Magento\Quote\Api\Data\TotalsItemExtensionFactory $totalItemExtensionFactory
     */
    public function __construct(
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepository,
        \Magento\Catalog\Helper\ImageFactory  $productImageHelper,
        \Magento\Quote\Api\Data\TotalsItemExtensionFactory $totalItemExtensionFactory    
    ) {
        $this->itemFactory = $itemFactory;
        $this->productRepository = $productRepository;
        $this->productImageHelper = $productImageHelper;
        $this->totalItemExtension = $totalItemExtensionFactory;
    }

    /**
     * add sku in total cart items
     *
     * @param  \Magento\Quote\Api\CartTotalRepositoryInterface $subject
     * @param  \Magento\Quote\Api\Data\TotalsInterface $totals
     * @return \Magento\Quote\Api\Data\TotalsInterface $totals
     */
    public function afterGet(
        \Magento\Quote\Api\CartTotalRepositoryInterface $subject,
        \Magento\Quote\Api\Data\TotalsInterface $totals
    ) {
        foreach($totals->getItems() as $item)
        {
            $quoteItem = $this->itemFactory->create()->load($item->getItemId());
            $product = $this->productRepository->create()->getById($quoteItem->getProductId());
            $extensionAttributes = $item->getExtensionAttributes();
            if ($extensionAttributes === null) {
                $extensionAttributes = $this->totalItemExtension->create();
            }

            $imageurl =$this->productImageHelper->create()->init($product, 'product_thumbnail_image')->setImageFile($product->getThumbnail())->getUrl();
            $extensionAttributes->setAppboxoImage($imageurl);
            $item->setExtensionAttributes($extensionAttributes);
        }

        return $totals;
    }
}