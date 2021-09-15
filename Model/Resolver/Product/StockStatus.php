<?php
declare(strict_types=1);

namespace Appboxo\Connector\Model\Resolver\Product;

use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;

/**
 * Class StockStatus
 * @package Mageplaza\StockStatusGraphQl\Model\Resolver\Product
 */
class StockStatus implements ResolverInterface
{
    protected $stockItem;
    
    public function __construct(\Magento\CatalogInventory\Api\StockStateInterface $stockItem)
    {
        $this->stockItem = $stockItem;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ){
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        $product = $value['model'];

        $result = $this->stockItem->getStockQty($product->getId(), $product->getStore()->getWebsiteId());

        return $result;
    }
}