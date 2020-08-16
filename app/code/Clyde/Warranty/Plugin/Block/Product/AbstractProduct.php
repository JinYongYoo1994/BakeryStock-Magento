<?php

namespace Clyde\Warranty\Plugin\Block\Product;

class AbstractProduct
{
    public function afterGetProductDetailsHtml(
        \Magento\Catalog\Block\Product\AbstractProduct $subject, 
        $result,
        $product
    ) {
         $customBlock = $subject->getLayout()->createBlock('Clyde\Warranty\Block\Product\View\Type\Configurable')->setProduct($product)->setTemplate('Clyde_Warranty::product/view/type/options/configurable.phtml')
          ->toHtml();
          return $result.$customBlock;
    }
}  