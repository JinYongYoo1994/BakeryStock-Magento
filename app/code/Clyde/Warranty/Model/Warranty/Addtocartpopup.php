<?php
namespace Clyde\Warranty\Model\Warranty;

use Magento\Framework\Data\OptionSourceInterface;
 
class Addtocartpopup implements OptionSourceInterface
{
    const SHOW_POPUP = 1;
    const NO_PLAN_POPUP = 2;
    const HIDE_POPUP = 3;
    
    public function getOptionArray()
    {
        $options = array(self::SHOW_POPUP => __('Always Show'), self::NO_PLAN_POPUP => __('Show only when no plan'), self::HIDE_POPUP => __('Dont show'));
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
