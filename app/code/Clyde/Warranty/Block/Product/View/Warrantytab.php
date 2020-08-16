<?php
namespace Clyde\Warranty\Block\Product\View;
use Magento\Catalog\Api\ProductRepositoryInterface;
class Warrantytab extends \Magento\Catalog\Block\Product\View
{
    public $_customerSession;
    public $_filterManager;
    public $_warrantyFactory;
    protected $_currency;
    protected $_productPrice;
    protected $_product;
    
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
        \Magento\Catalog\Model\Product $product,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_warrantyFactory = $warrantyFactory;
        $this->_helper = $helper;
        $this->_currency = $currency;
        $this->_product = $product;
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);
    }

    public function getProduct()
    {
        return $this->_product->load($this->getProductId());
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
            $price = $this->getFinalPrice();
            $productSku = $this->getProduct()->getSku();  
            $warrantyValue = $warranty->getWarrantryByPrice($product, $productSku);
            if (empty($warrantyValue) !== true) {
                $warrantyValue = $this->productFilterBlock($warrantyValue);
            }

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

    public function productFilterBlock($warrantyValue)
    {
        $tabOption = $this->_helper->getTabOptions();
        if ($tabOption == \Clyde\Warranty\Model\Config\Source\Options::CONCATENTE) {
            return $warrantyValue;
        } elseif ($tabOption == \Clyde\Warranty\Model\Config\Source\Options::FIRST_BLOCK) {
            return array($warrantyValue[0]);
        } elseif ($tabOption == \Clyde\Warranty\Model\Config\Source\Options::DONOT_SHOW) {
            return array();
        }
    }
}
