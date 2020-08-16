<?php
namespace Clyde\Warranty\Block\Product\View\Type;
use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;

class Configurable extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{
    protected $helper;
    protected $dataHelper;
    protected $catalogProduct;
    protected $configurableAttributeData;
    protected $jsonEncoder;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Clyde\Warranty\Helper\Product\Options $helper,
        \Clyde\Warranty\Helper\Data $dataHelper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        ConfigurableAttributeData $configurableAttributeData
    ) {

        parent::__construct(
            $context
        );
        $this->helper = $helper;
        $this->dataHelper = $dataHelper;
        $this->catalogProduct = $catalogProduct;
        $this->jsonEncoder = $jsonEncoder;
        $this->configurableAttributeData = $configurableAttributeData;
    }

    public function getModuleEnable()
    {
        return $this->dataHelper->getEnableModule();
    }

    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = array();
            $skipSaleableCheck = $this->catalogProduct->getSkipSaleableCheck();

            $allProducts = $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null);

            foreach ($allProducts as $product) {
                if ($product->isSaleable() || $skipSaleableCheck) {
                    $products[] = $product;
                }
            }

            $this->setAllowProducts($products);
        }

        return $this->getData('allow_products');
    }

    public function getJsonConfig()
    {

        $currentProduct = $this->getProduct();
        $config = array();
        if($currentProduct->getTypeId() == 'configurable'){
            $options = $this->helper->getOptions($currentProduct, $this->getAllowProducts());
            $config['index'] = isset($options['index']) ? $options['index'] : array();
        }else{
            $config['sku'] = $currentProduct->getSku();
        }        

        return $this->jsonEncoder->encode($config);
    }
    
}
