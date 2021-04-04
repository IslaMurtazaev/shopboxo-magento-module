<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Appboxo\Connector\Block\System\Config;


use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\ObjectManagerInterface;

class Generate extends \Magento\Config\Block\System\Config\Form\Field {

    /**
     * Path to block template
     */
    const CHECK_TEMPLATE = 'system/config/generate.phtml';

    public function __construct(Context $context,
                                $data = array())
    {
        parent::__construct($context, $data);
    }

    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::CHECK_TEMPLATE);
        }
        return $this;
    }

    public function getAjaxUrl()
    {
        return $this->getUrl('appboxo_connector/system_config/generate');
    }

    /**
     * Render Generate
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->addData(
            [
                'html_id' => 'appboxo_connector_gtb'
            ]
        );

        return $this->_toHtml();
    }

} 