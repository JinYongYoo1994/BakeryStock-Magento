<?php
namespace Clyde\Warranty\Model\Observer;

class productUpdateAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $warranty;

    protected $customerwarranty;

    protected $_helper;

    protected $orderFacory;

    protected $_request;

    protected $_currency;

    protected $_region;

    protected $_coreSession;

    protected $_plan;

    public function __construct(
        \Clyde\Warranty\Helper\Data $helper,
        \Clyde\Warranty\Model\Plan $planFactory,
        \Clyde\Warranty\Model\Customerwarranty $customerwarrantyFactory,
        \Magento\Sales\Model\Order $orderFacory,
        \Clyde\Warranty\Model\Warranty $warrantyFactory,
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
        $this->_plan = $planFactory; 
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helper->getEnableModule() == 1 ) {
            $item = $observer->getQuoteItem();
            $warranty_id = $this->_request->getParam('warranty');
            $itemid = $this->_request->getParam('itemid');
            $region_id = $this->_request->getParam('region_id');
            $rule_id = $this->_request->getParam('selected_rule_id');
            $update_type = $this->_request->getParam('update_type');
            $quoteItem = $this->_plan->getQuoteItemById($itemid);
            //print_r($this->_request->getParams()); exit;
            if ($warranty_id) {
                $warranty = $this->getWarrantyDetail($warranty_id, $item);
                $label = 'Warranty';              
                if($update_type != '' && $update_type == 'remove'){
                    $warrantyInfoString = '';

                    //$price = $this->_helper->convertAmount($item->getProduct()->getFinalPrice());

                    if(isset($quoteItem['item_base_price']) && $quoteItem['item_base_price'] != 0){
                        $itemBaseAmount = $quoteItem['item_base_price'];
                    }else{
                        $itemBaseAmount = $item->getProduct()->getFinalPrice();
                    }
                    
                    $price = $this->_helper->convertAmount($itemBaseAmount);

                    $additionalOptions = array();
                    if ($additionalOption = $item->getOptionByCode('additional_options')){
                        $option = $this->_helper->decryptString($additionalOption->getValue());
                        $additionalOptions = $this->checkOption($option);
                    }

                    $item->addOption(
                        array(
                        'product_id' => $item->getProductId(),
                        'code' => 'additional_options',
                        'value' => $this->_helper->encryptString($additionalOptions)
                        )
                    );
                }else{
                    $warrantyBaseAmount = $this->_helper->calculatePrice($warranty, $item->getProduct()); 
                    $warrantyCurrentAmount = $this->_helper->convertAmount($warrantyBaseAmount); 
                    $amount = $this->_currency->currency($warrantyBaseAmount, true, false);
                    
                    $regionValue = '';
                    if($region_id){
                        $regionData = $this->_region->load($region_id);
                        $regionValue = __('for %1 region', $regionData->getName().'('.$regionData->getCode().')');
                    }

                    $value = __($warranty['year_term'].' Year Plan %2 - %1', $amount, $regionValue);
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

                    $warrantyInfoString = $this->warrantyDetailAdded($warranty, $item, $rule_id);
                    $productPrice = $item->getProduct()->getFinalPrice();
                    $productCurrentPrice = $this->_helper->convertAmount($productPrice);
                    $price = $productCurrentPrice + $warrantyCurrentAmount;
                    $item->setItemBasePrice($productPrice);
                    $item->setWarrantyBasePrice($warrantyBaseAmount);
                }

                $item->setWarrantyInfo($warrantyInfoString);
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);
                $item->getProduct()->setIsSuperMode(true);
            }elseif($warranty_id == ''){
                $this->_coreSession->start();
                $this->_coreSession->setWarrantyApply(1);
            }
        }
        
        return $this;
    }


    public function getWarrantyDetail($warranty_id , $item)
    {
        $warranty = $this->warranty->getWarrantryRowBySku($item->getProductId(), $item->getProduct()->getSku(), $warranty_id);
        return $warranty;
        
    }

    public function checkOption($additionalOption)
    { 
         if (count($additionalOption)>0) {
            foreach ($additionalOption as $key=>$value) {
                if (strtolower($value['label']) === "warranty") {
                    unset($additionalOption[$key]);
                }
            }
         }
 
        return $additionalOption;
    }

    public function warrantyDetailAdded($warranty , $item ,$rule_id)
    {
        $product_price = $item->getPrice();
        $price = $this->_helper->calculatePrice($warranty, $item->getProduct());
        $ruleApplyProductPrice = $this->_helper->getWarrantyPrice($item->getProduct());
        $data = array('name'=>$warranty['category'],'plan_id'=>$warranty['sku'],'sku'=>$warranty['sku'],'customer_cost'=>$warranty['customer_cost'],'warranty_type'=>\Clyde\Warranty\Model\Warranty\Warrantytype::FIXED,'warranty_applied_price'=>$price,'product_price'=>$item->getProduct()->getFinalPrice(),'item_price'=>$item->getProduct()->getFinalPrice(),'rule_product_price'=>$ruleApplyProductPrice,'year_term'=>$warranty['year_term'],'rule_id'=>$rule_id);
        return $this->_helper->encryptString($data);
    }
}