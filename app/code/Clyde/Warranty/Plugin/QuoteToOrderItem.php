<?php
namespace Clyde\Warranty\Plugin;

use Closure;

class QuoteToOrderItem
{
    protected $_helper;
    public function __construct(
        \Clyde\Warranty\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);
        if(strlen($item->getWarrantyInfo())>2){
            if ($additionalOptionsQuote = $item->getOptionByCode('additional_options')) {
                    if ($additionalOptionsOrder = $orderItem->getProductOptionByCode('additional_options')) {
                        $additionalOptions = array_merge($additionalOptionsQuote, $additionalOptionsOrder);
                    } else {
                        $additionalOptions = $additionalOptionsQuote;
                    }
                   // print_r(json_encode($additionalOptions->getValue())); exit;
                    if (strlen($additionalOptions->getValue())>2) {
                        $options = $orderItem->getProductOptions();
                        $options['additional_options'] = $this->_helper->decryptString($additionalOptions->getValue());
                        $orderItem->setProductOptions($options);
                    }

                    $orderItem->setWarrantyInfo($item->getWarrantyInfo());
                    $originalSku = $orderItem->getSku();
                    $WarrantyInfo = $this->_helper->decryptString($item->getWarrantyInfo());
                    $warrantySku = $WarrantyInfo['sku'];
                    $updateSku = $originalSku.' - '.$warrantySku;
                    $orderItem->setSku($updateSku);
            }
        }
        
        return $orderItem;
    }

}