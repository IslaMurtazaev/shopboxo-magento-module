<?php
/**
 * Copyright © Appboxo. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Appboxo\Connector\Api\Data;

/**
 * Interface ConnectorInterface
 * @api
 */
interface ConnectorInterface
{
    /**
     * @return string|null
     */
    public function getPaymentid();

    /**
     * @param string $paymentid
     * @return null
     */
    public function setPaymentid($paymentid);
}
