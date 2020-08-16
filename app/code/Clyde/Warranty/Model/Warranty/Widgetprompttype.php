<?php
namespace Clyde\Warranty\Model\Warranty;

use Magento\Framework\Data\OptionSourceInterface;
 
class Widgetprompttype implements OptionSourceInterface
{
    const TYPE_SIMPLE = 'simple';
    const TYPE_SELECT = 'select';
    const TYPE_CART = 'cart';
    
    public function getOptionArray()
    {
        $options = array(self::TYPE_SIMPLE => __('Simple'), self::TYPE_SELECT => __('Select'), self::TYPE_CART => __('Cart'));
        return $options;
    }
    
    public function getAllOptions()
    {
        $res = $this->getOptions();
        array_unshift($res, array('value' => '', 'label' => ''));
        return $res;
    }
    
    public function getOptions()
    {
        $res = array();
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = array('value' => $index, 'label' => $value);
        }

        return $res;
    }
     
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
