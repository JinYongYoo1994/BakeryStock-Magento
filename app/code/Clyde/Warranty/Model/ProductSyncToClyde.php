<?php
namespace Clyde\Warranty\Model;
use Magento\Framework\App\Filesystem\DirectoryList;
class ProductSyncToClyde extends \Magento\Framework\Model\AbstractModel
{
    protected $_clydeApi;
    protected $_productCollectionFactory;
    protected $_ruleCollection;
    protected $_fillter;
    protected $_filesystem;
    protected $_files;
    protected $addedSku;
    protected $_clydeproduct;
    protected $_commandOutPut;
    protected $_helper;
    protected $_recordCount;
    protected $attributeSet;
    protected $selectedAttributeId = array();
    protected $planIds = array();
    protected $directory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, 
        \Clyde\Warranty\Model\Api\Clyde $clydeApi,
        \Clyde\Warranty\Model\Warranty $rule,
        \Magento\Framework\Filter\FilterManager $fillter,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $files,
        \Clyde\Warranty\Model\Clydeproduct $clydeproduct,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \Clyde\Warranty\Helper\Data $helper,
        array $data = array()
    ) {
        $this->_clydeApi = $clydeApi;
        $this->_ruleCollection = $rule;
        $this->_fillter = $fillter;
        $this->_filesystem = $filesystem;
        $this->_files = $files;
        $this->_helper = $helper;
        $this->_clydeproduct = $clydeproduct;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->attributeSet = $attributeSet;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context, $registry);
    }
    
    public function getProductCollection($limit = 10 , $page = 1)
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $collection->setPageSize($limit);
        $collection->setCurPage($page);
        return $collection;
    }

    public function getProductByApi($sku)
    {
        $clydeProduct = $this->_clydeApi->getProductBySku($sku);
        if (isset($clydeProduct['errors'])) {
            $data = $clydeProduct['errors'];
            if (isset($data[0]['code']) && $data[0]['code'] == '40402') {
                return false;
            }
        }

        return $clydeProduct;
    }

    public function getAttributeSetName($product) 
    {
        $attributeSetRepository = $this->attributeSet->get($product->getAttributeSetId());
        return $attributeSetRepository->getAttributeSetName();
    }

    public function getProductClydeData($product)
    {
        $attributes = $this->_helper->getExtendedAttributes();
        $storeMediaUrl = $this->_helper->getStoreImageMediaUrl($product->getStoreId(), $product);

        $data = array();
        $item['type'] = "product";
        $item['attributes']["name"] = $product->getName();
        $item['attributes']["type"] = $this->getAttributeSetName($product);
        $item['attributes']["sku"] = $product->getSku();
        $item['attributes']["description"] = $this->_fillter->truncate(strip_tags($product->getDescription()), array('length' => 20, 'etc' => ''));
        $item['attributes']["manufacturer"] = $product->getResource()->getAttribute($this->_helper->getProductArribute())->getFrontend()->getValue($product);
        $item['attributes']["barcode"] = '';
        if($this->_helper->getProductBarcodeArribute() != '') {
            $item['attributes']["barcode"] = $product->getResource()->getAttribute($this->_helper->getProductBarcodeArribute())->getFrontend()->getValue($product);
        }
        
        $item['attributes']["price"] = (float)$product->getPrice();
        $item['attributes']["imageLink"] = $storeMediaUrl;
        if (count($attributes) > 0) {
            $extendedAttributes = array();
            foreach ($attributes as $attribute) {
                $extendedAttributes[$attribute] = $product->getResource()->getAttribute($attribute)->getFrontend()->getValue($product);
            }

            $item['attributes']["attributes"] = $extendedAttributes;
        }

        
        $data['data'] = $item;
        
        return $data;
    }

    public function createProductInClyde($sku,$product, $id, $errorReturn = false)
    {
        $this->readFiles();
        $productData = $this->getProductClydeData($product);
        $data = $this->_clydeApi->getProductCreate($productData, $id);
        if (isset($data['errors'])) {
            $value = $data['errors'];
            if (isset($value[0]['code'])) {
                $detail = isset($value[0]['detail'])?$value[0]['detail']:'';
                $title = isset($value[0]['title'])?$value[0]['title']:'';
                $this->writeSkuCsv(array($sku,'On Create','Errors',$title.' - '.$detail));
                
                if ($errorReturn === true) {
                    return array('errors'=>$sku.' '.$title.', '.$detail);
                }
            }

            return false;
        }

        return array('responce_data'=>$data,'request_data'=>$productData);
    }

    public function updateProductInClyde($sku,$product, $id , $errorReturn = false)
    {
        $this->readFiles();
        $productData = $this->getProductClydeData($product);
        $data = $this->_clydeApi->getProductUpdate($sku, $productData, $id);
        if (isset($data['errors'])) {
            $value = $data['errors'];
            if (isset($value[0]['code'])) {
                $detail = isset($value[0]['detail'])?$value[0]['detail']:'';
                $title = isset($value[0]['title'])?$value[0]['title']:'';
                if(empty($this->addedSku) !== true){
                    $this->writeSkuCsv(array($sku,'On Update','Errors',$title.' - '.$detail));
                }
                
                if ($errorReturn === true) {
                    return array('errors'=>$sku.' '.$title.', '.$detail);
                }
            }
            
            return false;
        }

        return array('responce_data'=>$data,'request_data'=>$productData);
    }

    public function getSyncProduct($params)
    {
        $limit = isset($params['product_limit'])?$params['product_limit']:10;
        $page = isset($params['product_limit_page'])?$params['product_limit_page']:1;
        $products  = $this->getProductCollection($limit, $page);
        $percent = 0;
        $stop = 0;
        if ($page == 1) {
            $this->openFiles();
            $this->writeSkuCsv(array('SKU','Status','Clyde Status','Message'));
        } else {
            $this->readFiles();
        }
        
        if ($products->count()>0) {
            foreach ($products as $product) {
                $price = $product->getPrice();
                $productId = $product->getId();
                $warrantyValue = $this->_ruleCollection->getWarrantryByPrice($price, $productId);
                $result = $this->getProductValue($warrantyValue);
                
                $product->setClydeStatus(1);
                $product->setStoreId(0);
                if (isset($result['plan_ids']) && empty($result['plan_ids']) !== true) {
                $product->setClydeWarrantyPlans(implode(',', $result['plan_ids']));
                }

                if (isset($result['warranty_id']) && empty($result['warranty_id']) !== true) {
                    $product->setClydeEarrantyProgram($result['warranty_id']);
                }

                $isClyde = $this->getProductByApi($product->getSku());
                if ($isClyde === false) {
                    $add = $this->createProductInClyde($product->getSku(), $product, $product->getId());
                    if($add === true){
                        $product->setClydeLastSync(date('Y-m-d H:i:s'));
                    }
                } else {
                    $update = $this->updateProductInClyde($product->getSku(), $product, $product->getId());

                    if ($update === true) {
                        $product->setClydeLastSync(date('Y-m-d H:i:s'));
                    }
                }

                $product->save();
            }

            $page = $page+1;
            $percent = (($limit * $page)/$products->getSize())*100;
            $percent = round($percent);
        }

        if($percent >= 100){
            $stop = 1;
        }
        
        return array('error' => 0 , 'product_limit' => $limit , 'product_limit_page' => $page ,'totalCount'=>$percent, 'stop' => $stop);
    }

    public function getProductTypeId($productId)
    {
        return \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
    }
    
    public function openFiles() 
    {
        $files = 'product_import_on-'.date('y-m-d').'.csv';
        $this->addedSku = $this->directory->openFile($this->createSkuFile($files), 'w+');
    }

    public function readFiles() 
    {
        $files = 'product_import_on-'.date('y-m-d').'.csv';
        $dirPath = $this->setMediaDirectory();
        $filePath =  $dirPath.'/'. $files;
        $this->addedSku = $this->directory->openFile($filePath, 'a');
    }

    public function createSkuFile($file) 
    {
        $dirPath = $this->setMediaDirectory();
        $filePath =  $dirPath.'/'. $file;
        if (!file_exists($filePath)) {
            file_put_contents($filePath, '');
        }

        return $filePath;

    }

    public function setMediaDirectory()
    {
        $fileDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $dirPath =  $fileDirectory . "synccsv/".date('Y-m-d');

        if (!file_exists($dirPath)) 
        {
            $this->_files->mkdir($dirPath, 0777);
        }

        return $dirPath;
    }

    public function writeSkuCsv($value) 
    {
        $this->writeCsv($this->addedSku, $value);
    }

    public function writeCsv($resource, $value) 
    {
        $resource->writeCsv($value);
    }

    public function getProductCollectionCommand($limit = 10 , $page = 1)
    {
        //$productType = $this->_helper->getProductTypeSync();
        $collection = $this->_productCollectionFactory->create();
        $attributes = array('name','sku','price','type_id','description',$this->_helper->getProductArribute());
        if($this->_helper->getProductBarcodeArribute() != ''){
            $attributes[] = $this->_helper->getProductBarcodeArribute() ;
        }
        $collection->addAttributeToSelect($attributes);
        $collection->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $collection->setPageSize($limit);
        $collection->setCurPage($page);
        $this->_recordCount = $collection->getSize();
        return $collection;
    }

    public function getSyncProductCommandAndCron($output = null, $limit = 10 , $page = 1)
    {
        $products  = $this->getProductCollectionCommand($limit, $page);
        
        if ($page == 1) {
            $this->openFiles();
            $this->writeSkuCsv(array('SKU','Status','Clyde Status','Message'));
        } else {
            $this->readFiles();
        }

        if ($output) {
            $this->_commandOutPut = $output;
        }
        
        if ($products->count()>0) {
            foreach($products as $product){
                $price = $product->getPrice();
                $productId = $product->getId();
                $isClyde = $this->getProductByApi($product->getSku());
                if ($isClyde === false) {
                    $add = $this->createProductInClyde($product->getSku(), $product, $product->getId());
                    if ($add !== false) {
                        $returnData = $this->getClydeProductDataArray($product, json_encode($add['request_data']));
                        $this->_clydeproduct->getProductDataInTable($product->getSku(), $returnData);
                        if($output){
                            $output->writeln('<info>SKU: '.$product->getSku().' created successfully</info>');
                        }
                    }
                } else {
                    $productData = $this->getProductClydeData($product);
                    $isUpdate = $this->_clydeproduct->checkProductDetailSame($product->getSku(), $productData);
                    if($isUpdate === false){
                        $update = $this->updateProductInClyde($product->getSku(), $product, $product->getId());
                        if($update !== false){
                           $returnData = $this->getClydeProductDataArray($product, json_encode($update['request_data']));
                            $this->_clydeproduct->getProductDataInTable($product->getSku(), $returnData);
                            if($output){
                                $output->writeln('<info>SKU: '.$product->getSku().' updated successfully</info>');
                            }
                        }
                    }
                }
            }

            $page++;
            if (($page*$limit) <=  $this->_recordCount) {
                if ($output) {
                $output->writeln('<info> '.round((($page*$limit)/$this->_recordCount)*100).' % finished</info>');
                }

                $this->getSyncProductCommandAndCron($output, $limit, $page);
            }
        }
        
    }

    public function getClydeProductDataArray($product, $clydeJsonData)
    {
        return array('product_id'=>$product->getId(),
                     'sku'=>$product->getSku(),
                     'clyde_product_json'=>$clydeJsonData
                     );

    }

    public function getProductValue($warrantyValue)
    {
        $plan_ids = array();
        $warranty_id = array();
        if (empty($warrantyValue) !== true) {
            foreach ($warrantyValue as $warranty) {
                $plan_ids[] = $warranty['plan_id'];
                $warranty_id = $warranty['warranty_id'];
            }
        }

        return array('plan_ids'=>$plan_ids ,'warranty_id'=>$warranty_id );
    }
}