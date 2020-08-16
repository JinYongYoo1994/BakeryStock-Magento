<?php
namespace Clyde\Warranty\Block\Product;

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
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_warrantyFactory = $warrantyFactory;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }
    
    public function getHelper()
    {
        return $this->_helper;
    }
    
    public function getWarrantyByPrice()
    {
        $warranty = $this->_warrantyFactory;
        $price = 10;
        $product = 3;
        $warrantyValue = $warranty->getWarrantryByPrice($price, $product); 
    }
    
}
