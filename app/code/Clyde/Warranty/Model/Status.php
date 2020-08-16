<?php
namespace Clyde\Warranty\Model;
use Magento\Framework\Data\OptionSourceInterface;
 
class Status implements OptionSourceInterface
{
    const ENABLED = 1;
    const DISABLED = 2;
    
    public function getOptionArray()
    {
        $options = array(self::ENABLED => __('Enabled'), self::DISABLED => __('Disabled'));
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
