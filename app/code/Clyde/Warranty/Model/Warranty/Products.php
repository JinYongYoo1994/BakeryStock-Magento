<?php
namespace Clyde\Warranty\Model\Warranty;

use Magento\Framework\Data\OptionSourceInterface;
 
class Products implements OptionSourceInterface
{
    const FOR_ALL = 1;

    const FOR_SPECIFIC = 2;

    const FOR_BY_RULE = 3;
    
    public function getOptionArray()
    {
        $options = array(self::FOR_ALL => __('For All Products'), self::FOR_SPECIFIC => __('For Specific Products'), self::FOR_BY_RULE => __('By Rule'));
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
