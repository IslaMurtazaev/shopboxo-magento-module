<?php
/**
 * Copyright © Appboxo. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Appboxo\Connector\Api;

use Appboxo\Connector\Api\Data\ConnectorInterface;

/**
 * Interface for saving the checkout order paymentid
 * to the quote for guest users
 * @api
 */
interface GuestConnectorManagementInterface
{
    /**
     * @param string $cartId
     * @param ConnectorInterface $orderPaymentId
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function saveConnector(
        $cartId,
        ConnectorInterface $orderPaymentId
    );
}
