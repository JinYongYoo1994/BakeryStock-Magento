<?php
namespace Clyde\Warranty\Block;

class Warranty extends \Magento\Framework\View\Element\Template
{
    public $_customerSession;

    public $_filterManager;
    
    public $_warrantyFactory;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Clyde\Warranty\Model\Warranty $warrantyFactory,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_warrantyFactory = $warrantyFactory;
        $this->_helper = $helper;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }
    
    public function getHelper()
    {
        return $this->_helper;
    }
    
    public function getProduct()
    {        
        return $this->_registry->registry('current_product');
    }  
    
    public function getWarrantyDetail()
    {
        $warranty = $this->_warrantyFactory; 
        $product = $this->getProduct()->getId();  
        $productSku = $this->getProduct()->getSku();  
        $warrantyValue = $warranty->getWarrantryByPrice($product, $productSku);
        if (empty($warrantyValue) !== true ) {
            $warrantyValue['isAllowProduct'] = $this->isAllowProduct($warrantyValue);
        }

        return $warrantyValue;
    }
    
    public function isAllowProduct($data)
    {
        if ($data['products'] == \Clyde\Warranty\Model\Warranty\Products::FOR_SPECIFIC) {
          $product = $this->getProduct()->getId();
          $productsArray = $data['products_items'];
          if (empty($productsArray) !== true && in_array($product, $productsArray) ) {
            return true;
          }

          return false;
        } else {
          return true;
        }
    } 
}
