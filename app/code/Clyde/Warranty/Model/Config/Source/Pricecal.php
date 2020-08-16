<?php
namespace Clyde\Warranty\Model\Config\Source;
class Pricecal implements \Magento\Framework\Option\ArrayInterface
{
    const REGULAR = 1;
    const ACTUAL = 2;
    public function toOptionArray()
    {
        return array(
            array('value' => self::REGULAR, 'label' => __('Regular Price')),
            array('value' => self::ACTUAL, 'label' => __('Actual Price'))
        );
    }
}