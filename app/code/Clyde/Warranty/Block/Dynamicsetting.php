<?php
namespace Clyde\Warranty\Block;

class Dynamicsetting extends \Magento\Framework\View\Element\Template
{
    public $_customerSession;
    
    public $_filterManager;
    
    public $_helper;

    public $_cart;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Clyde\Warranty\Helper\Data $helper,
        array $data = array()
    ) {
        $this->_helper = $helper;
        $this->cart = $cart;
        parent::__construct($context, $data);
    }
    
    public function getHelper()
    {
        return $this->_helper;
    }

    public function getQuote()
    {
        return $this->cart->getQuote();
    }
    
}
