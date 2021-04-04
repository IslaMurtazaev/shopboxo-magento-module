<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Appboxo\Connector\Api;

interface CategoryRepositoryInterface
{
     /**
     * @api
     * @return json
     */
    public function getAppboxoProducts();
}