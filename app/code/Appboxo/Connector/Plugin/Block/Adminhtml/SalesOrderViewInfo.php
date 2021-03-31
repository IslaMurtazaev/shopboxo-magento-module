<?php
/**
 * Copyright © Appboxo. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Appboxo\Connector\Plugin\Block\Adminhtml;

use Appboxo\Connector\Model\Data\Connector;
use Magento\Sales\Block\Adminhtml\Order\View\Info as ViewInfo;

class SalesOrderViewInfo
{
    /**
     * @param ViewInfo $subject
     * @param string $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(
        ViewInfo $subject,
        $result
    ) {
      
        $paymentidBlock = $subject->getLayout()->getBlock('appboxo_order_paymentids'); 
        if ($paymentidBlock !== false) {
            $paymentidBlock->setConnector($subject->getOrder()
                ->getData(Connector::COMMENT_FIELD_NAME));
            $result = $result . $paymentidBlock->toHtml();
        }
        
        return $result;
    }
}