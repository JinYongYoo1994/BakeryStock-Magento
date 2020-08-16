<?php
namespace Clyde\Warranty\Block\Checkout\Cart;

use Magento\Framework\View\Element\Template\Context;

use Magento\Store\Model\ScopeInterface;

use Magento\Framework\View\Element\Template;

class Warranty extends \Magento\Checkout\Block\Cart\Additional\info
{
    protected $_warrantyFactory;
    
    public $_helper;
    
    protected $_currency;
    
    protected $_price;
    
    protected $_productPrice;
    
    public function __construct(
        Context $context,
        \Clyde\Warranty\Model\Warranty $warrantyFactory,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Framework\Pricing\Helper\Data $currency,
        \Magento\Bundle\Model\Product\Price $price,
        array $data = array()
    ) {
        $this->_warrantyFactory = $warrantyFactory;
        $this->_helper = $helper;
        $this->_currency = $currency;
        $this->_price = $price;
        parent::__construct($context, $data);
    }
    
    public function getModuleEnable()
    {
        return $this->_helper->getEnableModule();        
    }
    
    public function getWarrantyDetail()
    {
        $warranty = $this->_warrantyFactory;
        $product = $this->getItem()->getProductId(); 
        $warrantyValue = array();
        $productSku = $this->getItem()->getProduct()->getSku(); 
        $warrantyValue = $warranty->getWarrantryByPrice($product, $productSku);
        if (empty($warrantyValue) !== true ) {
            $warrantyValue = $warrantyValue[0];
            $warrantyValue['isAllowProduct'] = $this->isAllowProduct($warrantyValue);
        }
        
        return $warrantyValue;
        
    }
    
    public function getHelper()
    {
        return $this->_helper;
    }
    
    public function isAllowProduct($data)
    {
      return true; 
    }
    
    public function getOrderItem()
    {
        return $this->getItem();
    }
    
    public function calculatePrice($data)
    {
        $price = $this->_helper->calculatePrice($data, $this->getItem()->getProduct());
        return $this->_currency->currency($price, true, false);
    }
    
    public function getPrice($price)
    {
        return $this->_currency->currency($price, true, false);
    }
    
    public function getWarrantyPlan()
    {
        return '';
    }
    
    public function checkOption()
    {
        if ($additionalOption = $this->getItem()->getOptionByCode('additional_options')){
            $additionalOption = $this->_helper->decryptString($additionalOption->getValue());
            if (count($additionalOption)>0) {
                foreach ($additionalOption as $key=>$value) {
                    if (strtolower($value['label']) === "warranty") {
                        return true;
                    }
                }
            }
        }

       return false;
        
    }
    
    public function getWarrantyPrice($product)
    {
        $priceType = $this->_helper->getWarrantyPriceCalculation();
        if($priceType == \Clyde\Warranty\Model\Config\Source\Pricecal::REGULAR){
            $_productPrice = $product->getPrice();
        }else{
            if($product->getFinalPrice() > 0){
                $_productPrice = $product->getFinalPrice();
            }else{
                $_productPrice = $product->getPrice();
            }
        }

        return $_productPrice;
    }

    public function getWarrantyDetailOfItem()
    {
        $getWarrantyInfo = $this->getItem()->getWarrantyInfo();
        if (empty($getWarrantyInfo) !== true) {
            return $this->_helper->decryptString($getWarrantyInfo);
        }

        return false;
        
    }    
}
