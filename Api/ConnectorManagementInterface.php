<?php
/**
 * Copyright © Appboxo. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Appboxo\Connector\Api;

use Appboxo\Connector\Api\Data\ConnectorInterface;

/**
 * Interface for saving the checkout order paymentid
 * to the quote for logged in users
 * @api
 */
interface ConnectorManagementInterface
{
    /**
     * @param int $cartId
     * @param ConnectorInterface $orderPaymentId
     * @return string
     */
    public function saveConnector(
        $cartId,
        ConnectorInterface $orderPaymentId
    );

    /**
     * @param int $cartId
     * @param ConnectorInterface $orderPaymentId
     * @return string
     */
    public function saveOrderComment(
        $orderId,
        ConnectorInterface $orderPaymentId
    );
}
