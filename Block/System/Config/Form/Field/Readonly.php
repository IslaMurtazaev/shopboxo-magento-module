<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Appboxo\Connector\Block\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Readonly extends \Magento\Config\Block\System\Config\Form\Field
{    
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setData('readonly',1);
        return $element->getElementHtml();

    }
}