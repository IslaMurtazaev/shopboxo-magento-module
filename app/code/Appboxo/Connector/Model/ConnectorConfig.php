<?php
/**
 * Copyright © Appboxo. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Appboxo\Connector\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConnectorConfig implements ConfigProviderInterface
{
    /**
     *  Config Paths
     */
    const XML_PATH_GENERAL_IS_SHOW_IN_MYACCOUNT = 'appboxo_connector/general/is_show_in_myaccount';
    const XML_PATH_GENERAL_MAX_LENGTH = 'appboxo_connector/general/max_length';
    const XML_PATH_GENERAL_FIELD_STATE = 'appboxo_connector/general/state';
    
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    
    /**
     * @param    ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if show order paymentid to customer account
     *
     * @return bool
     */
    public function isShowPaymentIdInAccount()
    {
          return $this->scopeConfig->getValue(
              self::XML_PATH_GENERAL_IS_SHOW_IN_MYACCOUNT,
              ScopeInterface::SCOPE_STORE
          );
    }
    
    /**
     * Get order paymentid max length
     *
     * @return int
     */
    public function getConfig()
    {
        return [
            'max_length' => (int) $this->scopeConfig->getValue(
                self::XML_PATH_GENERAL_MAX_LENGTH,
                ScopeInterface::SCOPE_STORE
            ),
            'order_paymentid_default_state' => (int) $this->scopeConfig->getValue(
                self::XML_PATH_GENERAL_FIELD_STATE,
                ScopeInterface::SCOPE_STORE
            )
        ];
    }
}
