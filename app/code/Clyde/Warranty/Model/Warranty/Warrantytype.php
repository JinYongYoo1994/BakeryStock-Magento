<?php
namespace Clyde\Warranty\Model\Warranty;

use Magento\Framework\Data\OptionSourceInterface;
 
class Warrantytype implements OptionSourceInterface
{
    const FIXED = 1;
    const PERCENT = 2;
    
    public function getOptionArray()
    {
        $options = array(self::FIXED => __('By Fixed Amount'), self::PERCENT => __('By Percentage'));
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
