<?php
namespace Clyde\Warranty\Block\Product\View;
use Magento\Catalog\Api\ProductRepositoryInterface;
class Warrantydetail extends \Magento\Catalog\Block\Product\View
{
    public $_customerSession;
    public $_filterManager;
    public $_warrantyFactory;
    protected $_currency;
    protected $_productPrice;
    protected $_checkoutSession;
    
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Clyde\Warranty\Helper\Data $helper,
        \Clyde\Warranty\Model\Warranty $warrantyFactory,
        \Magento\Framework\Pricing\Helper\Data $currency,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_warrantyFactory = $warrantyFactory;
        $this->_helper = $helper;
        $this->_currency = $currency;
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);

    }

    public function getHelper()
    {
        return $this->_helper;
    }

    public function getModuleEnable()
    {
        
        return $this->_helper->getEnableModule();        
    }

    public function getLabel()
    {
        return $this->_helper->getWarrantylabel();        
    }

    public function getProductPriceCustom()
    {
        $data = $this->getWarrantyPrice($this->getProduct());
        return $data;      
    }

    public function getWarrantyDetail()
    {
            $warranty = $this->_warrantyFactory;
            $product = $this->getProduct()->getId(); 
            $productSku = $this->getProduct()->getSku(); 
            $price = $this->getProductPriceCustom(); 
            $warrantyValue = $warranty->getWarrantryByPrice($product, $productSku);
            return $warrantyValue;
    }

    public function calculatePrice($data)
    {
        $price = $this->_helper->calculatePrice($data, $this->getProduct());
        return $this->_currency->currency($price, true, false);
    }

    public function getWarrantyPrice($product)
    {
        if(empty($this->_productPrice) === true){
            $priceType = $this->_helper->getWarrantyPriceCalculation();
            if($priceType == \Clyde\Warranty\Model\Config\Source\Pricecal::REGULAR){
                $this->_productPrice = $product->getPrice();
            }else{
                if($product->getFinalPrice() > 0){
                    $this->_productPrice = $product->getFinalPrice();
                }else{
                    $this->_productPrice = $product->getPrice();
                }
            }
        }

        return $this->_productPrice;
    }

    public function getQuoteItemId()
    {
        $param = $this->getRequest()->getParams();
        return $param;
    }

    public function getAllSkus()
    {
        $product = $this->getProduct();
        $skus = array();
        $skus[] = $this->getProduct()->getSku();
        if($product->getTypeId() == 'configurable'){
            $_children = $product->getTypeInstance()->getUsedProducts($product);
            //$this->saveImageCsv($sku);
            foreach ($_children as $child){
               $skus[] = $child->getSku();
            }
        }

        return $skus;
    }

    public function getStorePrice($price)
    {
        return $this->_currency->currency($price, true, false);
    }

    public function getProtectionDetail($warranty)
    {
        return "Accident protection|Full replacement|No deductibles";
    }
}
