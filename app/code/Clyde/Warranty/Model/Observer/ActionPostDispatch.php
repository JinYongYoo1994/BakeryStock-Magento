<?php
namespace Clyde\Warranty\Model\Observer;

class ActionPostDispatch implements \Magento\Framework\Event\ObserverInterface
{
    protected $_helper;
    protected $_checkoutSession;
    protected $_request;
    protected $_currency;  

    public function __construct(
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Pricing\Helper\Data $currency
    ) {
        $this->_helper = $helper;
        $this->_checkoutSession = $_checkoutSession;
        $this->_currency = $currency;
        $this->_request = $request; 
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helper->getEnableModule() == 1) {
            $action = $this->_request->getFullActionName();
            if ($action == 'directory_currency_switch') {
                $quote = $this->_checkoutSession->getQuote();
                if ($quote && $quote->hasItems()) {
                    foreach ($quote->getAllVisibleItems() as $item){
                        //add condition for target item
                        if($item->getItemBasePrice() != 0){
                            $productPrice = $item->getItemBasePrice();
                            $productCurrentPrice = $this->_helper->convertAmount($productPrice);
                            if($item->getWarrantyInfo() != ''){
                                $warrantyBaseAmount = $item->getWarrantyBasePrice();
                                $warrantyCurentAmount = $this->_helper->convertAmount($warrantyBaseAmount);
                                $productCurrentPrice = $productCurrentPrice + $warrantyCurentAmount;
                                if ($additionalOption = $item->getOptionByCode('additional_options')) {
                                    $additionalOptions = $this->_helper->decryptString($additionalOption->getValue());
                                    $option = $this->_helper->decryptString($additionalOption->getValue());
                                    $additionalOptions = $this->checkOption($option, $item);
                                    if (count($additionalOptions) > 0) {
                                        $item->addOption(
                                            array(
                                            'product_id' => $item->getProductId(),
                                            'code' => 'additional_options',
                                            'value' => $this->_helper->encryptString($additionalOptions)
                                            )
                                        );
                                    }
                                }
                            }

                            $item->setCustomPrice($productCurrentPrice);
                            $item->setOriginalCustomPrice($productCurrentPrice);
                            $item->getProduct()->setIsSuperMode(true);
                            $quote->collectTotals()->save();
                        }
                    }
                }
            }
        }

        return $this;
    }

    public function checkOption($additionalOption, $item)
    {
        $warrantyDecryptData = $this->_helper->decryptString($item->getWarrantyInfo());

        if (count($additionalOption)>0 && count($warrantyDecryptData)>0) {
            foreach ($additionalOption as $key=>$value) {
                if (strtolower($value['label']) === "warranty") {
                    $warrantyBaseAmount = $item->getWarrantyBasePrice();
                    $amount = $this->_currency->currency($warrantyBaseAmount, true, false);
                    $value = __($warrantyDecryptData['year_term'].' Year Plan - %1', $amount);
                    $additionalOption[$key]['value'] = $value;
                }
            }
        }

        return  $additionalOption;
    }

}