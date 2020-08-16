<?php
namespace Clyde\Warranty\Block\Product\View;

class Tab extends Warrantydetail
{
    public function getWarrantyDetail()
    {
        $warrantyValue = parent::getWarrantyDetail();
        if (empty($warrantyValue) !== true) {
            $warrantyValue = $this->productFilterBlock($warrantyValue);
        }

        return $warrantyValue;
    }
    
    public function productFilterBlock($warrantyValue)
    {
        $tabOption = $this->_helper->getTabOptions();
        if ($tabOption == \Clyde\Warranty\Model\Config\Source\Options::CONCATENTE) {
            return array($warrantyValue[0]);
        } elseif ($tabOption == \Clyde\Warranty\Model\Config\Source\Options::FIRST_BLOCK) {
            return array($warrantyValue[0]);
        } elseif ($tabOption == \Clyde\Warranty\Model\Config\Source\Options::DONOT_SHOW) {
            return array();
        }
    }
}
