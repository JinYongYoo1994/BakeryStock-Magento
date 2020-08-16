<?php
namespace Clyde\Warranty\Model\Warranty;

use Magento\Framework\Data\OptionSourceInterface;
 
class Producttype implements OptionSourceInterface
{
    const TYPE_SIMPLE = 'simple';
    const TYPE_CONFIGURABLE = 'configurable';
    const TYPE_VIRTUAL = 'virtual';
    const TYPE_BUNDLE = 'bundle';
    const TYPE_DOWNLOADBLE = 'downloadable';
    const TYPE_GROUPED = 'grouped';
    
    public function getOptionArray()
    {
        $options = array(self::TYPE_SIMPLE => __('Simple Product'), self::TYPE_CONFIGURABLE => __('Configurable Product'), self::TYPE_BUNDLE => __('Bundle Product'), self::TYPE_GROUPED => __('Grouped Product'), self::TYPE_VIRTUAL => __('Virtual Product'), self::TYPE_DOWNLOADBLE => __('Downloadable Product'));
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
