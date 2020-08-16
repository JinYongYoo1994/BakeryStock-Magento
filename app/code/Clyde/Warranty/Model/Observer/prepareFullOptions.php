<?php
namespace Clyde\Warranty\Model\Observer;

class prepareFullOptions implements \Magento\Framework\Event\ObserverInterface
{
    protected $warranty;

    protected $customerwarranty;

    protected $_helper;

    protected $orderFacory;

    protected $_request;

    protected $_currency;

    protected $_region;

    protected $_coreSession;

    public function __construct(
        \Clyde\Warranty\Helper\Data $helper,
        \Clyde\Warranty\Model\Plan $planFactory,
        \Clyde\Warranty\Model\Customerwarranty $customerwarrantyFactory,
        \Clyde\Warranty\Model\Warranty $warrantyFactory,
        \Magento\Sales\Model\Order $orderFacory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Pricing\Helper\Data $currency,
        \Magento\Directory\Model\Region $region,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        $this->_helper = $helper;
        $this->warranty = $warrantyFactory;
        $this->customerwarranty = $customerwarrantyFactory;
        $this->orderFacory = $orderFacory;
        $this->_request = $request;
        $this->_currency = $currency;
        $this->_region = $region; 
        $this->_coreSession = $coreSession; 
    }
    
    public function getValidValue($data)
    {   
        if(is_array($data)){
            foreach($data as $key=>$value){
                if($value != ''){
                    return $value;
                }
            }
        }else{
            return $data;
        }
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helper->getEnableModule() == 1) {
            $item = $observer->getQuoteItem();
            $warranty_id = $this->getValidValue($this->_request->getParam('warranty'));
            $rule_id = $this->getValidValue($this->_request->getParam('selected_rule_id'));
            $contract_detail = $this->getValidValue($this->_request->getParam('contract_detail'));
            $post = $this->_request->getParams();
            //file_put_contents('/var/www/html/clyde/var/log/post.log','Wattanty_is'.json_encode($warranty_id).PHP_EOL, FILE_APPEND | LOCK_EX);
            //file_put_contents('/var/www/html/clyde/var/log/post.log',json_encode($post).PHP_EOL, FILE_APPEND | LOCK_EX);
            if ($warranty_id && $contract_detail != '') {
                $contractDetailArray = json_decode($contract_detail, true);
                $label = 'Warranty';
                $warrantyBaseAmount = isset($contractDetailArray['recommendedPrice'])?$contractDetailArray['recommendedPrice']:0; 
                $warrantyCurrentAmount = $this->_helper->convertAmount($warrantyBaseAmount); 
                $amount = $this->_currency->currency($warrantyBaseAmount, true, false);
                
                $regionValue = '';
                $turm = $this->getWrrantyYearTurm($contractDetailArray);
                $value = __($turm.' Year Plan %2 - %1', $amount, $regionValue);
                $additionalOptions[] = array(
                    'label' => $label,
                    'value' => $value
                );
                if (count($additionalOptions) > 0) {
                    $item->addOption(
                        array(
                        'product_id' => $item->getProductId(),//Missing data
                        'code' => 'additional_options',
                        'value' => $this->_helper->encryptString($additionalOptions)
                        )
                    );
                }
 
                $productPrice = $item->getProduct()->getFinalPrice();
                $productCurrentPrice = $this->_helper->convertAmount($productPrice);
                $price = $productCurrentPrice + $warrantyCurrentAmount;
                $item->setWarrantyInfo($this->warrantyDetailAdded($contractDetailArray, $item, $rule_id));
                $item->setCustomPrice($price);
                $item->setItemBasePrice($productPrice);
                $item->setWarrantyBasePrice($warrantyBaseAmount);
                $item->setOriginalCustomPrice($price);
                $item->getProduct()->setIsSuperMode(true);
            }elseif($warranty_id == ''){
                $this->_coreSession->start();
                $this->_coreSession->setWarrantyApply(1);
            }
        }
        
        return $this;
    }

    public function getWrrantyYearTurm($warranty)
    {
        return isset($warranty['term'])?$warranty['term']:''; 
    }

    public function warrantyDetailAdded($warranty , $item ,$rule_id)
    {
        $turm = $this->getWrrantyYearTurm($warranty);
        $product_price = $item->getPrice();
        $price = isset($warranty['recommendedPrice'])?$warranty['recommendedPrice']:0; 
        $ruleApplyProductPrice = $this->_helper->getWarrantyPrice($item->getProduct());
        $data = array('name'=>"Product Protection",'plan_id'=>$warranty['sku'],'sku'=>$warranty['sku'],'customer_cost'=>$warranty['recommendedPrice'],'warranty_type'=>\Clyde\Warranty\Model\Warranty\Warrantytype::FIXED,'warranty_applied_price'=>$price,'product_price'=>$item->getProduct()->getFinalPrice(),'item_price'=>$item->getProduct()->getFinalPrice(),'rule_product_price'=>$ruleApplyProductPrice,'year_term'=>$turm,'rule_id'=>$rule_id);
        return $this->_helper->encryptString($data);
    }
}