<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Appboxo\Connector\Api;

interface ProductRepositoryInterface
{
    /**
     * @api
     * @param string $sku
     * @return array
     */
    public function getProductImageUrl($sku);
}