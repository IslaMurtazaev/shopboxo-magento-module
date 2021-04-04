<?php
/**
 * Copyright © Appboxo. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Appboxo\Connector\Block\Order;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Appboxo\Connector\Model\Data\Connector;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Appboxo\Connector\Model\ConnectorConfig;

class PaymentId extends Template
{
    /**
     * @var ConnectorConfig
     */
    protected $orderPaymentIdConfig;
    
    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @param    Context $context
     * @param    Registry $registry
     * @param    ConnectorConfig $orderPaymentIdConfig
     * @param   array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ConnectorConfig $orderPaymentIdConfig,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->orderPaymentIdConfig = $orderPaymentIdConfig;
        $this->_isScopePrivate = true;
        $this->_template = 'order/view/paymentids.phtml';
        parent::__construct($context, $data);
    }
    
    /**
     * Check if show order paymentid to customer account
     *
     * @return bool
     */
    public function isShowPaymentIdInAccount()
    {
        return $this->orderPaymentIdConfig->isShowPaymentIdInAccount();
    }
    
    /**
     * Get Order
     *
     * @return array|null
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * Get Payment ID
     *
     * @return string
     */
    public function getConnector()
    {
        return trim($this->getOrder()->getData(Connector::COMMENT_FIELD_NAME));
    }

    /**
     * Retrieve html paymentid
     *
     * @return string
     */
    public function getConnectorHtml()
    {
        return nl2br($this->escapeHtml($this->getConnector()));
    }

    /**
     * Check if has order paymentid
     *
     * @return bool
     */
    public function hasConnector()
    {
        return strlen($this->getConnector()) > 0;
    }
}
