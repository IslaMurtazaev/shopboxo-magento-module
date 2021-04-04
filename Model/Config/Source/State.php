<?php
/**
 * Copyright © Appboxo. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Appboxo\Connector\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class State implements ArrayInterface
{
    /**
     * State values
     */
    const COLLAPSED = 0;
    const EXPANDED = 1;
    const NO_COLLAPSE_EXPAND = 2;
    
    /**
     * Get options
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::COLLAPSED => __('Collapsed'),
            self::EXPANDED => __('Expanded'),
            self::NO_COLLAPSE_EXPAND => __('Without Collapse/Expand')
        ];
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->toArray() as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $optionArray;
    }
}
