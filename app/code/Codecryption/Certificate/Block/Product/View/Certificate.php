<?php
namespace Codecryption\Certificate\Block\Product\View;
use Magento\Catalog\Api\ProductRepositoryInterface;
class Certificate extends \Magento\Catalog\Block\Product\View 
{
    public $_customerSession;
    public $_filterManager;
    public $_warrantyFactory;
    protected $_currency;
    protected $_productPrice;
    protected $_checkoutSession;
    protected $_helper;
    
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
        \Codecryption\Certificate\Helper\Data $helper,
        \Magento\Framework\Pricing\Helper\Data $currency,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->_currency = $currency;
        parent::__construct($context, $urlEncoder, $jsonEncoder,$string,$productHelper,$productTypeConfig,$localeFormat, $customerSession, $productRepository, $priceCurrency, $data);

    }

    public function getHelper()
    {
        return $this->_helper;
    }

    public function getModuleEnable()
    {
        
        return $this->_helper->getEnableModule();        
    }

    public function getMapField()
    {
        return $this->_helper->getMapedField();        
    }

    public function getCertificateDetails()
    {
        $this->getProduct();
    }
}
