<?php
namespace Clyde\Warranty\Plugin\CustomerData;

use Magento\Checkout\Model\Session as CheckoutSession;

class DefaultItem extends \Magento\Checkout\CustomerData\DefaultItem
{
    protected $_plan;
    protected $_warranty;
    protected $_helper;
    protected $_storeManager;
    protected $_date;

    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Msrp\Helper\Data $msrpHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Clyde\Warranty\Model\Plan $plan,
        \Clyde\Warranty\Model\Warranty $warranty,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date

    ) {
        parent::__construct($imageHelper, $msrpHelper, $urlBuilder, $configurationPool, $checkoutHelper);
        $this->_plan = $plan;
        $this->_warranty = $warranty;
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_date =  $date;
    }

    protected function doGetItemData()
    {
        
        $imageHelper = $this->imageHelper->init($this->getProductForThumbnail(), 'mini_cart_product_thumbnail');
        $productName = $this->item->getProduct()->getName();
        $timestemp = (strtotime($this->item->getUpdatedAt()) < 0)?strtotime($this->item->getCreatedAt()):strtotime($this->item->getUpdatedAt());
        $d1 = $this->_date->timestamp();;
        $d2 = $this->_date->timestamp($timestemp);;
        return array(
            'options' => $this->getOptionList(),
            'qty' => $this->item->getQty() * 1,
            'item_id' => $this->item->getId(),
            'configure_url' => $this->getConfigureUrl(),
            'is_visible_in_site_visibility' => $this->item->getProduct()->isVisibleInSiteVisibility(),
            'product_id' => $this->item->getProduct()->getId(),
            'product_name' => $productName,
            'product_sku' => $this->item->getProduct()->getSku(),
            'product_url' => $this->getProductUrl(),
            'product_has_url' => $this->hasProductUrl(),
            'product_price' => $this->checkoutHelper->formatPrice($this->item->getCalculationPrice()),
            'product_price_value' => $this->item->getCalculationPrice(),
            'weight' => $this->item->getProduct()->getWeight(),
            'product_image' => array(
                'src' => $imageHelper->getUrl(),
                'alt' => $imageHelper->getLabel(),
                'width' => $imageHelper->getWidth(),
                'height' => $imageHelper->getHeight(),
            ),
            'canApplyMsrp' => $this->msrpHelper->isShowBeforeOrderConfirm($this->item->getProduct())
                && $this->msrpHelper->isMinimalPriceLessMsrp($this->item->getProduct()),
            'warranty_info' => $this->item->getWarrantyInfo(),
            'timestemp' => $timestemp,
            'time_diffrence' => $d1-$d2,
            'quote_id' => $this->item->getQuote()->getId(),
            //'warranty_item_info' => $this->getWarrantyData($this->item)
        );
    }

   /* public function getWarrantyData($item)
    {
        $responce = '';
        if($item->getWarrantyInfo() != ''){
            $_warrantyFactory = $this->_plan;
            $_ruleFactory = $this->_warranty;
            $shippingAddress = $item->getQuote()->getShippingAddress();
            $warrantyData = $item->getWarrantyInfo(); 
            $warranty_info = $this->_helper->decryptString($warrantyData);
            if (isset($warranty_info['plan_id'])) {
            //$warranty = $_warrantyFactory->load($warranty_info['plan_id']);
            $rule = $_ruleFactory->load($warranty_info['rule_id']);
                if ($rule->getRegionalWrranty() == 1 && $rule->getRegionOption() == \Clyde\Warranty\Model\Warranty\Regionoption::VALIDATE ) {
                    $warranty_item = array('year_term'=>$warranty_info['year_term'],
                                           'itemid'=>$item->getId(),
                                           "quote"=>$item->getQuote()->getId(),
                                           "warranty"=>$warranty_info['plan_id'],
                                           "page"=>"checkout",
                                           'product_name'=>$item->getName(),
                                           'clyde_sku'=>$warranty_info['sku'],
                                           "country_ids"=>explode(',',$rule->getCountryId()),
                                           "warranty_error_message"=>$this->_helper->getWarrantyError(),
                                           'remove_url'=>$this->_storeManager->getStore()->getUrl('warranty/index/removewarranty')
                                           );
                    $responce =  $this->_helper->encryptString($warranty_item);
                } 
            }
        }
        return $responce;
    }*/
}
