<?php

namespace Clyde\Warranty\Plugin\Block\Cart\Item;

class Renderer
{
    protected $_helper;

    public function __construct(
        \Clyde\Warranty\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    public function beforeGetFormatedOptionValue(
        \Magento\Checkout\Block\Cart\Item\Renderer $subject, 
        $optionValue
    ) {
      if($subject->getItem()->getWarrantyInfo() != ''){
          if(isset($optionValue['label']) &&  strtolower($optionValue['label']) == strtolower('Warranty')){
              $functionDef = 'removecartWarranty("xulumus-product-warranty-dropdown-'.$subject->getItem()->getId().'")';
              $html = '<a style="vertical-align: sub;" href="javascript:void(0)" data-part="#xulumus-product-warranty-dropdown-'.$subject->getItem()->getId().'" class="clyde-delete" onclick="removecartWarranty(this)"><img  style="margin-left: 5px; height:16px; width:16px" src="'.$subject->getViewFileUrl('Clyde_Warranty/images/cart-close.svg').'"/></a>';
          		$optionValue['value'] = $optionValue['value'].$html;
          }
        }
          return array($optionValue);
    }
}  