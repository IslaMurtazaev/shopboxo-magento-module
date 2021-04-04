<?php
/**
 * Copyright © Appboxo. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Appboxo\Connector\Model\Data;

use Appboxo\Connector\Api\Data\ConnectorInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class Connector extends AbstractSimpleObject implements ConnectorInterface
{
    const COMMENT_FIELD_NAME = 'order_paymentid';
    
    /**
     * @return string|null
     */
    public function getPaymentid()
    {
        return $this->_get(static::COMMENT_FIELD_NAME);
    }

    /**
     * @param string $paymentid
     * @return $this
     */
    public function setPaymentid($paymentid)
    {
        return $this->setData(static::COMMENT_FIELD_NAME, $paymentid);
    }
}
