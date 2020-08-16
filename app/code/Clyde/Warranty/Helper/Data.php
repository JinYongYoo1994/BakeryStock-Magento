<?php
namespace Clyde\Warranty\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\SerializerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_moduleReader;

    protected $authSession;

    protected $_currency;

    protected $_version;

    protected $_moduleList;

    protected $_orderManager;

    protected $_storeManager;
    protected $_productType;
    protected $_writeConfig;
    protected $_imageHelperFactory;
    protected $_appEmulation;
    protected $_pubPath;
    protected $serializer;
    protected $_request;
    protected $_config;


    CONST PAGINATION = 5;

    CONST WARRANTY_LABEL = "Protection Plan";

    CONST WARRANTY_ERROR = "Your shippping region does not match with warranty";

    CONST COLOR_CODE = "DEAD3C";
    CONST COLOR_CODE_TEXT = "FFFFFF";
    CONST PRODUCT_SYNC_LIMIT = 1000;
    CONST WIDGET_SANDBOX_URL = 'https://sandbox-api.joinclyde.com/scripts/custom-script.min.js';
    CONST WIDGET_PRODUCTION_URL = 'https://api.joinclyde.com/scripts/custom-script.min.js';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $authSession, 
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Pricing\Helper\Data $currency,
        \Magento\Framework\App\ProductMetadata $version,
        \Magento\Sales\Model\Order $orderManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Clyde\Warranty\Model\Warranty\Producttype $productType,
        \Magento\Framework\App\Config\Storage\Writer $writeConfig,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Config\Model\Config $config,
        \Magento\Store\Model\App\Emulation $appEmulation

    ) {
        $this->_moduleReader = $moduleReader;
        $this->authSession = $authSession;
        $this->_currency = $currency;
        $this->_version = $version;
        $this->_moduleList = $moduleList;
        $this->_orderManager = $orderManager;
        $this->_objectManager = $objectmanager;
        $this->_storeManager = $storeManager;
        $this->_productType = $productType;
        $this->_writeConfig = $writeConfig;
        $this->_imageHelperFactory = $imageHelperFactory;
        $this->_appEmulation = $appEmulation;
        $this->_appEmulation = $appEmulation;
        $this->_appEmulation = $appEmulation;
        $this->_config = $config;
        $this->_request = $context->getRequest();

        parent::__construct($context);                    
    }
    
    public function getEnableModule()
    {
        $isEnable = $this->scopeConfig->getValue('clyde_warranty/general/warranty_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $isClydeConnect = $this->isClydeConnect();
        if($isEnable == 1 && $isClydeConnect == 1){
            return 1;
        }

        return 0;
    }
    
    public function getWarrantuPagination()
    {
        return self::PAGINATION;
    }
    
    public function getWarrantyPriceCalculation()
    {
        return \Clyde\Warranty\Model\Config\Source\Pricecal::REGULAR;
    }
    
    public function getWarrantyPrice($product)
    {
        
        $priceType = $this->getWarrantyPriceCalculation();
        if($priceType == \Clyde\Warranty\Model\Config\Source\Pricecal::REGULAR){
            $productPrice = $product->getPrice();
        }else{
            if($product->getFinalPrice() > 0){
                $productPrice = $product->getFinalPrice();
            }else{
                $productPrice = $product->getPrice();
            }
        }

        return $productPrice;
    }
    
    public function calculatePrice($data , $product)
    {
       //$product_price = $this->getWarrantyPrice($product);
       return $data['customer_cost'];
    
    }
    
    public function getTabOptions()
    {
        return 1;
    }
    
    public function getWarrantyError()
    {
        
        return self::WARRANTY_ERROR;
    }
    
    private function getSerializer()
    {
        if ($this->serializer === null) {
            $this->serializer = $this->_objectManager->get(SerializerInterface::class);
        }
        return $this->serializer;
    }

    public function encryptString($string)
    {

      return $this->getSerializer()->serialize($string);
      
    }
    
    public function decryptString($string)
    {
      return $this->getSerializer()->unserialize($string);
    }
    
    public function getMagentoVersion()
    {
        $version =  $this->_version->getVersion();
        $version = str_replace('.', '', $version);
        return $version;
    }
    
    public function getWarrantylabel()
    {
        return self::WARRANTY_LABEL;
    }

    public function getAddtocartPopup()
    {
        $label = $this->scopeConfig->getValue('clyde_warranty/general/addtocart_popup', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return ($label !='')?$label:Clyde\Warranty\Model\Warranty\Addtocartpopup::SHOW_POPUP;
    }

    public function getExtensionVersion()
    {
        $moduleCode = 'Clyde_Warranty';
        $moduleInfo = $this->_moduleList->getOne($moduleCode);
        return $moduleInfo['setup_version'];
    }

    public function getClydeAPIkey()
    {
        $scopeCode = $this->getAdminStoreValue();
        $apikey = $this->scopeConfig->getValue('clyde_warranty/general/clyde_API', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
        return trim($apikey);
    }

    public function getAdminStoreValue($type = 'code')
    {
        $storeId = (int) $this->_request->getParam('store', 0);
        $store = $this->_storeManager->getStore($storeId);
        if($store->getCode() != 'admin'){
            if($type == 'code'){
                return $store->getCode();
            }else{
                return $store->getId();
            }
        }else{
            return null;
        }
        
        
    }

    public function getClydeSecretkey()
    {
        $scopeCode = $this->getAdminStoreValue();
        $secretkey = $this->scopeConfig->getValue('clyde_warranty/general/clyde_secret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
        return trim($secretkey);
    }
    
    public function getClydeErrorMessage()
    {
        $message = $this->scopeConfig->getValue('clyde_warranty/general/clyde_error_msg', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return trim($message);
    }

    public function getClydeurl()
    {
    	$scopeCode = $this->getAdminStoreValue();
        $url = $this->scopeConfig->getValue('clyde_warranty/general/clyde_api_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
        return trim($url);
    }

    public function setClydeConnect($value)
    {
        $scopeCode = $this->getAdminStoreValue('id');
        $this->_writeConfig->save('clyde_warranty/general/api_connect', $value,$this->_config->getScope() ,$scopeCode);
        $this->scopeConfig->clean();
    }

    public function getClydeConnectData()
    {
        $scopeCode = $this->getAdminStoreValue();
        return $this->scopeConfig->getValue('clyde_warranty/general/api_connect_data', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$scopeCode);
    }

    public function setClydeConnectData()
    {
        $scopeCode = $this->getAdminStoreValue('id');
        $data = $this->getClydeConnectData();
        if($data != ''){
            $data = json_decode($data, true);
        }else{
            $data = array();
        }

        $key = $this->getClydeAPIkey();
        $sectrate = $this->getClydeSecretkey();
        $insertedValue = $key.'_'.$sectrate;
        if(isset($data[$insertedValue]) && $data[$insertedValue] == 1){
            return false;
        }else{
            $data[$insertedValue] = 1;
            $data = json_encode($data);
            $this->_writeConfig->save('clyde_warranty/general/api_connect_data', $data, $this->_config->getScope(), $scopeCode);
            $this->scopeConfig->clean();
            return true;
        }

        return false;
    }


    public function isClydeConnect()
    {
        $scopeCode = $this->getAdminStoreValue();
        return $this->scopeConfig->getValue('clyde_warranty/general/api_connect', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    public function getWidgetPromptType()
    {
        $type = $this->scopeConfig->getValue('clyde_warranty/general/widget_prompt_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
       return ($type !='')?$type:\Clyde\Warranty\Model\Warranty\Widgetprompttype::TYPE_SIMPLE;
    }

    public function getWidgetType()
    {
        $type = $this->scopeConfig->getValue('clyde_warranty/general/widget_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
       return ($type !='')?$type:\Clyde\Warranty\Model\Warranty\Widgettype::TYPE_PRODUCTPAGE;
    }

    public function getClydeProccessMode()
    {
        $url = $this->getClydeurl();
        if (strpos($url, 'sandbox') === false) {
            return 'production';
        }else{
            return 'sandbox';
        }
    }

    public function getClydeWidgetUrl()
    {
        $url = $this->getClydeurl();
        if (strpos($url, 'sandbox') === false) {
            return self::WIDGET_PRODUCTION_URL;
        }else{
            return self::WIDGET_SANDBOX_URL;
        }
    }

    public function getProductLimit()
    {
        return self::PRODUCT_SYNC_LIMIT;
    }

    public function getProductArribute()
    {
        $url = $this->scopeConfig->getValue('clyde_warranty/product_sync_tab/product_arrtibute_code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $url = trim($url);
        return ($url !='')?$url:'manufacturer';
    }

    public function getProductBarcodeArribute()
    {
        $barcode = $this->scopeConfig->getValue('clyde_warranty/product_sync_tab/product_arrtibute_upc', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $barcode = trim($barcode);
        return ($barcode !='')?$barcode:'';
    }

    //reurnts pub/ or / depending on webserver configuration, used for magento CLI as it has no knowledge of /pub
    public function detectPub()
    {
       //TODO this functions needs to be a adjusted to verify if the web is on /pub or not, currently if forces remove pub
       // see https://github.com/magento/magento2/issues/8868
        if (!isset($this->_pubPath)) {
            $this->_pubPath = '/';

        }
        return $this->_pubPath;

    }
    public function getStoreImageMediaUrl($storeId = null, $product){
        $pubPath = $this->detectPub();
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $imageUrl = $mediaUrl.'catalog/product'.$product->getImage();
      return $imageUrl;
    }
    public function getWarrantyCount($orderData)
    {
        $warrantyid = array();

        foreach ($orderData->getItemsCollection() as $item) {
            $items = $item->getOrderItem(); 
            if(!empty($items['warranty_info'])){
                $warrantyid[] = $items['product_id'];
            }
        }

        return $warrantyid;
    }

    public function getProductTypeSync()
    {
        // $type = $this->scopeConfig->getValue('clyde_warranty/product_sync_tab/clyde_sync_product_type',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        // if($type !=''){
        // 	$typeProduct = explode(',', $type);
        // }else{
        // 	$typeProduct = array(\Clyde\Warranty\Model\Warranty\Producttype::TYPE_SIMPLE, \Clyde\Warranty\Model\Warranty\Producttype::TYPE_VIRTUAL);
        // };
        $typeProduct = array_keys($this->_productType->getOptionArray());
        //print_r($typeProduct); exit;
        //$typeProduct = array(\Clyde\Warranty\Model\Warranty\Producttype::TYPE_SIMPLE);
        return $typeProduct;
    }

    public function getButtonSelectedColor()
    {
        
        return self::COLOR_CODE;
    }

    public function getButtonSelectedTextColor()
    {
        
        return self::COLOR_CODE_TEXT;
    }

    public function getPlanSelectedColor()
    {
        return self::COLOR_CODE;
    }

    public function getPlanSelectedColorText()
    {
        return self::COLOR_CODE;
    }

    private function isSerialized($value)
    {
        return (boolean) preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }

    public function getExtendedAttributes()
    {
        $value = $this->scopeConfig->getValue('clyde_warranty/product_sync_tab/extended_attributes', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (empty($value)) return false;

        if ($this->isSerialized($value)) {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Unserialize\Unserialize::class);
        } else {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
        }

        $products = $unserializer->unserialize($value);
        return $this->getAttributesArray($products);
    }

    public function getAttributesArray($products)
    {
        $values = array();
        if(count($products)>0){
            foreach($products as $sku){
                $values[] = $sku['attributes'];
            }
        }

        return $values;
    }

    public function convertAmount($amountValue) 
    {
        $current_currency = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        return $this->_storeManager->getStore()->getBaseCurrency()->convert($amountValue, $current_currency);
    }

}
