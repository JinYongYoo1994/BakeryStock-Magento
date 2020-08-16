<?php
namespace Clyde\Warranty\Model;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProTypeModel;

class Product extends \Magento\Framework\Model\AbstractModel
{
    protected $_storeManager;
    
    protected $_countryFactory;
    
    protected $messageManager;
    
    const CACHE_TAG = 'clyde_warranty';
    
    protected $_cacheTag = 'clyde_warranty';
   
    protected $_eventPrefix = 'clyde_warranty';
    
    protected $_transportBuilder = '';
    
    protected function _construct()
    {
        $this->_init('Clyde\Warranty\Model\ResourceModel\Product');
    }
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Directory\Model\CountryFactory $CountryFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        ConfigurableProTypeModel $configurableProTypeModel,
        \Magento\Catalog\Model\Product $product,
        array $data = array()
    ) {
        $this->_storeManager = $store;
        $this->_countryFactory = $CountryFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
        $this->_configurableProTypeModel = $configurableProTypeModel;
        $this->_product = $product;
        parent::__construct($context, $registry);
    }
    
    public function setWarrantyProduct($id ,$data, $condition_rule_type = null)
    {
       if(isset($data['category_products']) && $data['category_products'] != ''){
            $decode = json_decode($data['category_products'], true);
            $products = array_keys($decode);
            $data = $this->processRuleProducts($id, $products, $condition_rule_type);
            return $data;
       }
       
    }

    
    public function processRuleProducts($id ,$products, $condition_rule_type = null)
    {
        
       $data = $this->getDeleteWarrantyProduct($id);
       $data = $this->getInsertWarrantyProduct($id, $products, $condition_rule_type);
       return $data;
       
    }

    public function setProductWarranty($productId , $warranties)
    {
       if(empty($productId) !== true){
            $data = $this->_getResource()->getDeleteWarrantyProduct($productId, 'product_id');
            $data = $this->_getResource()->getInsertProductWarranty($productId, $warranties);
            return $data;
       }
       
    }
    
    public function getInsertWarrantyProduct($id, $products, $condition_rule_type = null)
    {
        return $this->_getResource()->getInsertWarrantyProduct($id, $products, $condition_rule_type);
    }
    

    public function getWarrantyProduct($warranty_id, $product_id = null, $condition_rule_type = null)
    {
        return $this->_getResource()->getWarrantyProduct($warranty_id, $product_id, $condition_rule_type);
    }
    
    public function getDeleteWarrantyProduct($id)
    {
        return $this->_getResource()->getDeleteWarrantyProduct($id);
    }

    public function getResourceConnection()
    {
        return $this->_getResource()->getConnection();
    }

    public function getProductIdBySku($sku = null) 
    {
        $connection = $this->getResourceConnection();
        $table = $connection->getTableName('catalog_product_entity'); 
        $select = $connection->select()
            ->from(array('main' =>$table), array('entity_id','sku'))
            ->where('main.sku = ?', $sku);
        
        return $connection->fetchRow($select);

    }

    public function getConfAssoProductId($productId, $nameValueList)
    {
        $product = $this->_product->load($productId); 
        $optionsData = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
        $assPro = $this->_configurableProTypeModel->getProductByAttributes($nameValueList, $product);
        if($assPro){
            $assocateProId = $assPro->getSku();
        }else{
            $assocateProId = $product->getSku();
        }
        
        return $assocateProId;
    }
    
}