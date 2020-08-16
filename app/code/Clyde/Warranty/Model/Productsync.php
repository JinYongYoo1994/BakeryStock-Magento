<?php
namespace Clyde\Warranty\Model;
 
class Productsync extends \Magento\Framework\Model\AbstractModel
{
    
    protected $_productCollection;

    protected $_ruleCollection;

    protected $_planCollection;

    protected $_productCollectionFactory;

    protected $_clydeproductCollection;

    protected $_objectManager;

    protected $attributeRepository;

    protected $attributeValues;

    protected $tableFactory;

    protected $attributeOptionManagement;

    protected $optionLabelFactory;

    protected $optionFactory;

    protected $productUrl;
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Clyde\Warranty\Model\Warranty $rule,
        \Clyde\Warranty\Model\Plan $plan,
        \Clyde\Warranty\Model\Api\Clyde $clydeproductCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, 
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Model\Entity\Attribute\Source\TableFactory $tableFactory,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement,
        \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $optionLabelFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory,
        \Magento\Catalog\Model\Product\Url $productUrl,
        array $data = array()
    ) {
        $this->_ruleCollection = $rule;
        $this->_planCollection = $plan;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_clydeproductCollection   = $clydeproductCollection;
        $this->_productRepository = $productRepository;
        $this->_objectManager = $objectmanager;

        $this->attributeRepository = $attributeRepository;
        $this->tableFactory = $tableFactory;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->optionLabelFactory = $optionLabelFactory;
        $this->optionFactory = $optionFactory;
        $this->productUrl = $productUrl;

        parent::__construct($context, $registry);
    }
    
    public function getProductCollection($limit = 10 , $page = 1)
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->setPageSize($limit);
        $collection->setCurPage($page);
        return $collection;
    }

    public function getSyncProduct($params)
    {
        $limit = isset($params['product_limit'])?$params['product_limit']:10;
        $page = isset($params['product_limit_page'])?$params['product_limit_page']:1;
        $products  = $this->getProductCollection($limit, $page);
        $percent = 0;
        $stop = 0;
        if($products->count()>0){
            foreach($products as $product){
                $price = $product->getPrice();
                $productId = $product->getId();
                $warrantyValue = $this->_ruleCollection->getWarrantryByPrice($price, $productId);
                $result = $this->getProductValue($warrantyValue);
                $product->setClydeLastSync(date('Y-m-d H:i:s'));
                $product->setClydeStatus(1);
                $product->setStoreId(0);
                if(isset($result['plan_ids']) && empty($result['plan_ids']) !== true){
                $product->setClydeWarrantyPlans(implode(',', $result['plan_ids']));
                }

                if(isset($result['warranty_id']) && empty($result['warranty_id']) !== true){
                    $product->setClydeEarrantyProgram($result['warranty_id']);
                }
                
                $product->save();
            }

            $page++;
            $percent = (($limit * $page)/$products->getSize())*100;
            $percent = round($percent);
        }

        if($percent >= 100){
            $stop = 1;
        }
        
        return array('product_limit' => $limit , 'product_limit_page' => $page ,'totalCount'=>$percent, 'stop' => $stop);
    }

    public function getProductValue($warrantyValue)
    {
        $plan_ids = array();
        $warranty_id = array();
        if(empty($warrantyValue) !== true){
            foreach($warrantyValue as $warranty){
                $plan_ids[] = $warranty['plan_id'];
                $warranty_id = $warranty['warranty_id'];
            }
        }

        return array('plan_ids'=>$plan_ids ,'warranty_id'=>$warranty_id );
    }

    public function getSyncData($page)
    {
        $stop = 0;
        $data = $this->_clydeproductCollection->getClydeProduct($page);
        $this->getProductData($data['data']);
        $totalpage = $data['meta']['totalPages'];
        if($page <= $totalpage){
            $page = $page + 1;
        }else{
            $stop = 1;
        }

        $percent = round(($page / $totalpage)*100);
        return array('product_limit_page' => $page ,'totalCount'=>$percent, 'stop' => $stop,'product_limit' => 0);
    }

    public function getProductData($data)
    {
        if(sizeof($data)>0){
            foreach ($data as $productdata) {
                try{
                   $_product = $this->_productRepository->get($productdata['attributes']['sku']);
                   if($_product->getId()){
                      $this->updateProduct($_product->getId(), $productdata['attributes']);
                   }
                }catch(\Magento\Framework\Exception\NoSuchEntityException $e){
                    $_product = false;
                     $this->updateProduct(0, $productdata['attributes']);
                }
            }
        }
    }

    public function getAttributeSetId($productId)
    {
        return 4;
    }

    public function getProductStatus($productId)
    {
        return 1;
    }

    public function getProductWeight($productId)
    {
        return 10;
    }

    public function getProductVisibility($productId)
    {
        return 4;
    }

    public function getProductTaxClass($productId)
    {
        return 2;
    }

    public function getProductTypeId($productId)
    {
        return \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
    }
        
    public function updateProduct($productId,$data)
    {
        
        if($productId == 0){
            $product = $this->_objectManager->create('\Magento\Catalog\Model\Product');
        }else{
            $product = $this->_productRepository->getById($productId);
        }

        $product->setSku($data['sku']);
        $product->setName($data['name']);
        $product->setAttributeSetId($this->getAttributeSetId($productId));
        $product->setStatus($this->getProductStatus($productId));
        $product->setWeight($this->getProductWeight($productId));
        $product->setVisibility($this->getProductVisibility($productId));
        $product->setTaxClassId($this->getProductTaxClass($productId));
        $product->setManufacturer($this->createOrGetId('manufacturer', $data['manufacturer']));
        $product->setTypeId($this->getProductTypeId($productId));
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);
        $product->setUrlKey($this->getProductUrlKey($product, $data));
        $product->setStockData(
            array(
                                    'use_config_manage_stock' => 0,
                                    'manage_stock' => 1,
                                    'is_in_stock' => 1,
                                    'qty' => 100
                                )
        );
        $product->save();
    }
    
    protected function getProductUrlKey($product, $rowData)
    {
        if ($product->getId() != '') {
            return $this->productUrl->formatUrlKey($rowData['name'].'-'.$rowData['sku']);
        }

        return '';
    }

    public function getAttribute($attributeCode)
    {
        return $this->attributeRepository->get($attributeCode);
    }

    public function createOrGetId($attributeCode, $label)
    {
        if (strlen($label) < 1) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Label for %1 must not be empty.', $attributeCode)
            );
        }

        // Does it already exist?
        $optionId = $this->getOptionId($attributeCode, $label);

        if (!$optionId) {
            // If no, add it.

            /** @var \Magento\Eav\Model\Entity\Attribute\OptionLabel $optionLabel */
            $optionLabel = $this->optionLabelFactory->create();
            $optionLabel->setStoreId(0);
            $optionLabel->setLabel($label);

            $option = $this->optionFactory->create();
            $option->setLabel($optionLabel);
            $option->setStoreLabels(array($optionLabel));
            $option->setSortOrder(0);
            $option->setIsDefault(false);

            $this->attributeOptionManagement->add(
                \Magento\Catalog\Model\Product::ENTITY,
                $this->getAttribute($attributeCode)->getAttributeId(),
                $option
            );

            // Get the inserted ID. Should be returned from the installer, but it isn't.
            $optionId = $this->getOptionId($attributeCode, $label, true);
        }

        return $optionId;
    }

    public function getOptionId($attributeCode, $label, $force = false)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $this->getAttribute($attributeCode);

        // Build option array if necessary
        if ($force === true || !isset($this->attributeValues[ $attribute->getAttributeId() ])) {
            $this->attributeValues[ $attribute->getAttributeId() ] = array();

            // We have to generate a new sourceModel instance each time through to prevent it from
            // referencing its _options cache. No other way to get it to pick up newly-added values.

            /** @var \Magento\Eav\Model\Entity\Attribute\Source\Table $sourceModel */
            $sourceModel = $this->tableFactory->create();
            $sourceModel->setAttribute($attribute);

            foreach ($sourceModel->getAllOptions() as $option) {
                $this->attributeValues[ $attribute->getAttributeId() ][ $option['label'] ] = $option['value'];
            }
        }

        // Return option ID if exists
        if (isset($this->attributeValues[ $attribute->getAttributeId() ][ $label ])) {
            return $this->attributeValues[ $attribute->getAttributeId() ][ $label ];
        }

        // Return false if does not exist
        return false;
    }
}