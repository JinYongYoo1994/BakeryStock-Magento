<?php
namespace Clyde\Warranty\Model;

use Magento\Quote\Model\Quote\Address;

use Magento\Rule\Model\AbstractModel;
 
class Warranty extends AbstractModel
{
    protected $_helper;

    protected $logger;

    protected $_storeManager;

    protected $_countryFactory;

    protected $messageManager;

    const CACHE_TAG = 'clyde_warranty';

    protected $_cacheTag = 'clyde_warranty';
   
    protected $_eventPrefix = 'clyde_warranty';

    protected $_transportBuilder = '';

    protected $_productFactory;

    protected $customerwarranty;

    protected $_rule;

    protected $condCombineFactory;

    protected $condProdCombineF;

    protected $validatedAddresses = array();

    protected $_productIds;

    protected $_productCollectionFactory;

    protected $_productsFilter = null;

    protected $_resourceIterator;

    protected $_productModelFactory;

    protected $_planFactory;

    protected $_allowedProduct = array();

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Clyde\Warranty\Model\ResourceModel\Warranty');
        $this->setIdFieldName('warranty_id');
    }
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Directory\Model\CountryFactory $CountryFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Clyde\Warranty\Model\Product $productModelFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
        \Clyde\Warranty\Model\Customerwarranty $customerwarrantyFactory,
        \Clyde\Warranty\Model\Plan $planFactory,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $condProdCombineF,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = array()
    ) {
        $this->_helper = $helper;
        $this->logger = $context->getLogger();
        $this->_storeManager = $store;
        $this->_countryFactory = $CountryFactory;
        $this->_productFactory = $productFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
        $this->customerwarranty = $customerwarrantyFactory;
        $this->condCombineFactory = $condCombineFactory;
        $this->condProdCombineF = $condProdCombineF;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_resourceIterator = $resourceIterator;
        $this->_planFactory = $planFactory;
        $this->_productModelFactory = $productModelFactory;
        $this->_allowedProduct = $this->_helper->getProductTypeSync();
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }
    public function sendTemplate()
    {
        $this->setSubject($this->_helper->getEmailSubject());
        $this->setName('Admin');
        $country = $this->_countryFactory->create()->load($this->getCountryId());
        $this->setCountryId($country->getName());
        $from = array('name' =>$this->getFirstname(),'email' => $this->getEmail());
        $email = $this->_helper->getEmailSendTo();
        $_transportBuilder = $this->_transportBuilder;
        try {
            $transport = $_transportBuilder->setTemplateIdentifier('trade_email_template')
                ->setTemplateOptions(
                    array('area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID)
                )
                ->setTemplateVars(array('data' => $this))
                ->setFrom($from)
                ->addTo($email)
                ->setReplyTo($this->getEmail())
                ->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        
    }
    
    public function getProductItems($product_id = null)
    {
        $id = $this->getId();
        $produts = array();
        if ($this->getId() != '') {
            if ($this->getProducts() == \Clyde\Warranty\Model\Warranty\Products::FOR_SPECIFIC) {
                $produts = $this->_productModelFactory->getWarrantyProduct($this->getId(), $product_id);
            }
        }

        return $produts;
    }
    
    public function getWarrantryByPrice($product, $productSku = null)
    {
        $reloadedProduct = $this->_productFactory->create()->load($product);
        $data = array();
        if(in_array($reloadedProduct->getTypeId(), $this->_allowedProduct))
        {
            $data = $this->_planFactory->getWarrantyPlanByRuleId($productSku);
        }
        
        return $data;
    }

    public function getWarrantryRowBySku($product, $productSku, $contractSku)
    {
        $warranties = $this->getWarrantryByPrice($product, $productSku);
        if(count($warranties)>0){
            foreach($warranties as $warrantyItem){
                if(isset($warrantyItem['sku']) && $warrantyItem['sku'] == $contractSku){
                    return $warrantyItem;
                }
            }
        }

        return array();
    }

    public function getWarrantryByPrice1($product)
    {
        $data = array();
        $collection = $this->getCollection();
        $collection->addFieldToFilter('status', \Clyde\Warranty\Model\Status::ENABLED);
        $collection->getSelect()->order('sort_order ASC');
        //$this->checkWarrantyProduct($collection, $product);
        return $collection;
    }

    public function checkWarrantyProduct($collection, $product)
    {
        if ($collection->count()) {
            foreach ($collection as $item) {
                if($item->getProducts() == \Clyde\Warranty\Model\Warranty\Products::FOR_ALL) {
                } elseif ($item->getProducts() == \Clyde\Warranty\Model\Warranty\Products::FOR_SPECIFIC) {
                    $warranty = $this->_productModelFactory->getWarrantyProduct($item->getId(), $product, \Clyde\Warranty\Model\Warranty\Products::FOR_SPECIFIC);
                    if(empty($warranty) === true){
                         $collection->removeItemByKey($item->getId());
                    }
                } elseif ($item->getProducts() == \Clyde\Warranty\Model\Warranty\Products::FOR_BY_RULE) {
                    $items = $this->checkWarrantyItemCondition($item, $product);
                    if (empty($items) === true) {
                        $collection->removeItemByKey($item->getId());
                    }
                }
            }
        }

        return $collection;
    }
    
    public function getMatchingProductIds()
    {
        if ($this->_productIds === null) {
            $this->_productIds = array();
            $this->setCollectedAttributes(array());
                $productCollection = $this->_productCollectionFactory->create();
                $productCollection->addWebsiteFilter('1');
                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }

                $this->getConditions()->collectValidatedAttributes($productCollection);
                $this->_resourceIterator->walk(
                    $productCollection->getSelect(),
                    array(array($this, 'callbackValidateProduct')),
                    array(
                        'attributes' => $this->getCollectedAttributes(),
                        'product' => $this->_productFactory->create()
                    )
                );
        }

        return $this->_productIds;
    }
   
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);
        $websites = $this->_getWebsitesMap();
        $results = array();
        foreach ($websites as $websiteId => $defaultStoreId) {
            $product->setStoreId($defaultStoreId);
            if ($this->getConditions()->validate($product)) {
                $this->_productIds[] = $product->getId();
            }
        }
    }
   
    protected function _getWebsitesMap()
    {
        $map = array();
        $websites = $this->_storeManager->getWebsites();
        foreach ($websites as $website) {
            // Continue if website has no store to be able to create catalog rule for website without store
            if ($website->getDefaultStore() === null) {
                continue;
            }

            $map[$website->getId()] = $website->getDefaultStore()->getId();
        }

        return $map;
    }
    
    public function setProductsFilter($productIds)
    {
        $this->_productsFilter = $productIds;
    }

    public function getProductsFilter()
    {
        return $this->_productsFilter;
    }
    
    public function checkWarrantyItemCondition($item, $product)
    {
        //$productIds = $item->getMatchingProductIds();
         $warranty = $this->_productModelFactory->getWarrantyProduct($item->getId(), $product, \Clyde\Warranty\Model\Warranty\Products::FOR_BY_RULE);
        if(empty($warranty) === false){
            return true;
        }

        return false;
    }
    
    public function getCustomerWarranty()
    {
        return $this->customerwarranty;
    }
    
    
    public function getConditionsInstance()
    {
        return $this->condCombineFactory->create();
    }
   
    public function getActionsInstance()
    {
        return $this->condProdCombineF->create();
    }
    
    public function getWarrantyProducts()
    {
        $data = array();
        $collection = $this->getCollection();
        $collection->addFieldToFilter('status', \Clyde\Warranty\Model\Status::ENABLED);
        $collection->getSelect()->order('sort_order ASC');
        $result = $this->checkWarrantyProductAssign($collection, $data);
        $ids = array();
        if ($collection->count()>0) {
           foreach ($collection as $item) {
                $ids[] = $item->getId();
           } 
        }

        return array('products'=>$result, 'plans' =>$ids);
    }

    public function checkWarrantyProductAssign($collection, $data)
    {
        if ($collection->count()) {
            $data = array();
            foreach ($collection as $item) {
                if($item->getProducts() == \Clyde\Warranty\Model\Warranty\Products::FOR_ALL) {
                   $data['all'] = 'all';
                } elseif ($item->getProducts() == \Clyde\Warranty\Model\Warranty\Products::FOR_SPECIFIC) {
                    $specic = $this->_productModelFactory->getWarrantyProduct($item->getId(), null);
                    if(empty($specic) === true){
                         $collection->removeItemByKey($item->getId());
                    }else{
                         $data = array_merge($data, $specic);
                    }
                } elseif ($item->getProducts() == \Clyde\Warranty\Model\Warranty\Products::FOR_BY_RULE) {
                    $condition = $item->getMatchingProductIds();
                    if (empty($condition) === true) {
                        $collection->removeItemByKey($item->getId());
                    }else{
                        $data = array_merge($data, $condition);
                    }
                }
            }
        }

        return array_unique($data);
    }

    public function getWarrantyProductsPriceForSync($price,$ids)
    {
        $data = $this->_planFactory->getWarrantyPlanByRuleId($ids, $price);
        return $data;
    }
    
}