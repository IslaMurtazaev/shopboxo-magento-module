<?php
/**
 * Copyright © Appboxo. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Appboxo\Connector\Model;

use Appboxo\Connector\Api\GuestConnectorManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Appboxo\Connector\Api\ConnectorManagementInterface;
use Appboxo\Connector\Api\Data\ConnectorInterface;

class GuestConnectorManagement implements GuestConnectorManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var ConnectorManagementInterface
     */
    protected $orderPaymentIdManagement;
    
    /**
     * GuestConnectorManagement constructor.
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param ConnectorManagementInterface $orderPaymentIdManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        ConnectorManagementInterface $orderPaymentIdManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->orderPaymentIdManagement = $orderPaymentIdManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function saveConnector(
        $cartId,
        ConnectorInterface $orderPaymentId
    ) {
        $quoteIdMask = $this->quoteIdMaskFactory->create()
            ->load($cartId, 'masked_id');
                            
        return $this->orderPaymentIdManagement->saveConnector(
            $quoteIdMask->getQuoteId(),
            $orderPaymentId
        );
    }
}
