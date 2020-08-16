<?php
namespace Clyde\Warranty\Block\Checkout;

class Warranty extends \Magento\Framework\View\Element\Template
{
    protected $_checkoutSession;
    
    protected $_warrantyFactory;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Clyde\Warranty\Model\Warranty $warrantyFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->_warrantyFactory = $warrantyFactory;
        parent::__construct($context, $data);
    }
    
    public function getRemovedWarranty()
    {
      $warranty = $this->_warrantyFactory;
      $cartQuote=$this->checkoutSession->getQuote();
      $items=$cartQuote->getAllItems();
      $shippingAddress = $cartQuote->getShippingAddress();
        foreach ($items as $item) {
            $warrantyData = $item->getWarrantyInfo(); 
            $warranty_info = $this->_helper->decryptString($warrantyData);
            if (isset($warranty_info['warranty_id'])) {
                $warranty = $this->_warrantyFactory->load($warranty_info['warranty_id']);
                if ($warranty->getRegionalWrranty() == 1 && $warranty->getRegionOption() == \Clyde\Warranty\Model\Warranty\Regionoption::VALIDATE ) {
                    $validateAddress = $warranty->validateWarrantyRegion($shippingAddress);
                    if ($validateAddress !== true) {
                        $warranty_item = array('warranty_plan'=>$warranty->getWarrantyPlan(),'itemid'=>$item->getId(),"quote"=>$item->getQuote()->getId(),"warranty"=>$warranty->getId(),"page"=>"checkout",'product_name'=>$item->getName(),"region_id"=>$shippingAddress->getRegionId());
                        $responce[] = $warranty_item;
                    }   
                }
            }   
        }

        return $responce;
    }
}
