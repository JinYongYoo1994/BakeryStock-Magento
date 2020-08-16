<?php
namespace Clyde\Warranty\Model\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class ImportBunchSaveAfter implements ObserverInterface
{
    protected $_warrantysale;

    protected $_helper;

    protected $messageManager;

    protected $_orderManager;

    protected $_apiClyde;

    protected $_productSync;

    protected $_clydeproduct;

    protected $_clydeApi;

    protected $_fillter;
    protected $_clydeItem;
    protected $_importlogger;
    

    public function __construct(
        \Clyde\Warranty\Model\WarrantysaleFactory $warrantysale,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Clyde\Warranty\Model\Api\Clyde $apiClyde,
        \Clyde\Warranty\Model\ProductSyncToClyde $productSync,
        \Clyde\Warranty\Model\Clydeproduct $clydeproduct,
        \Clyde\Warranty\Model\Api\Clyde $clydeApi,
        \Magento\Framework\Filter\FilterManager $fillter,
        \Clyde\Warranty\Model\Product $clydeItem,
        \Clyde\Warranty\Model\Logger $logger
    ) {
        $this->_warrantysale = $warrantysale;
        $this->_helper = $helper;
        $this->messageManager = $messageManager;
        $this->_apiClyde = $apiClyde;
        $this->_productSync = $productSync;
        $this->_clydeproduct = $clydeproduct;
        $this->_clydeApi = $clydeApi;
        $this->_fillter = $fillter;
        $this->_clydeItem = $clydeItem;
        $this->_importlogger = $logger;

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->_helper->getEnableModule() == 1){
            $bunchs = $observer->getBunch();
            $digits = 3;
            $productId = (int)str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
            
            if(count($bunchs) > 0){
                $this->_importlogger->errorLog('Product import start');
                foreach($bunchs as $bunch){
                    if(isset($bunch['sku']) && $bunch['sku'] != '' && isset($bunch['description'])  && isset($bunch[$this->_helper->getProductArribute()]) && isset($bunch['attribute_set_code']) && isset($bunch['price'])){
                        $productDetail = $this->_clydeItem->getProductIdBySku($bunch['sku']);
                        $productId = isset($productDetail['entity_id'])?$productDetail['entity_id']:$productId;

                        $isClyde = $this->_productSync->getProductByApi($bunch['sku']);

                        if($isClyde === false){
                            $add = $this->createProductInClyde($bunch['sku'], $bunch, $productId, true);
                            if(isset($add['errors'])){
                                   $this->_importlogger->errorLog($add['errors']);
                            }elseif($add !== false){
                                $returnData = $this->getClydeProductDataArray($bunch, json_encode($add['request_data']), $productDetail);
                                $this->_clydeproduct->getProductDataInTable($bunch['sku'], $returnData);
                            }
                        }else{
                            $productData = $this->getProductClydeData($bunch);
                            
                            $isUpdate = $this->_clydeproduct->checkProductDetailSame($bunch['sku'], $productData);
                            if($isUpdate === false){
                                $update = $this->updateProductInClyde($bunch['sku'], $bunch, $productId, true);
                                if(isset($update['errors'])){
                                    $this->_importlogger->errorLog($add['errors']);
                                }elseif($update !== false){
                                   $returnData = $this->getClydeProductDataArray($bunch, json_encode($update['request_data']), $productDetail);
                                   $this->_clydeproduct->getProductDataInTable($bunch['sku'], $returnData);
                                }
                            }
                        }
                    }
                }

                $this->_importlogger->errorLog('Product import end');
            }
        }
        
    }

    public function getClydeProductDataArray($product, $clydeJsonData, $productDetail)
    {
        return array('product_id'=>isset($productDetail['entity_id'])?$productDetail['entity_id']:'',
                     'sku'=>isset($product['sku'])?$product['sku']:'',
                     'clyde_product_json'=>$clydeJsonData
                     );
    }

    public function getProductClydeData($product)
    {
        $data = array();
        $description = isset($product['description'])?$product['description']:'';
        if($description != ''){
            $description = $this->_fillter->truncate(strip_tags($description), array('length' => 20, 'etc' => ''));
        }

        $manufacturer = isset($product[$this->_helper->getProductArribute()])?$product[$this->_helper->getProductArribute()]:'';
        $price = isset($product['price'])?$product['price']:'';

        $item['type'] = "product";
        $item['attributes'] = array("name"=> isset($product['name'])?$product['name']:'',
                                  "type"=> isset($product['attribute_set_code'])?$product['attribute_set_code']:'',
                                  "sku"=> isset($product['sku'])?$product['sku']:'',
                                  "description"=>$description ,
                                  "manufacturer"=> $manufacturer,
                                  "barcode"=> "",
                                  "price"=> (float)$price,
                                  "imageLink"=> ""
                            );
        $data['data'] = $item;
        return $data;
    }

    public function createProductInClyde($sku,$product, $id, $errorReturn = false)
    {
        $productData = $this->getProductClydeData($product);
        $data = $this->_clydeApi->getProductCreate($productData, $id);
        if(isset($data['errors'])){
            $value = $data['errors'];
            if(isset($value[0]['code'])){
                $detail = isset($value[0]['detail'])?$value[0]['detail']:'';
                $title = isset($value[0]['title'])?$value[0]['title']:'';
                return array('errors'=>$sku.' '.$title.', '.$detail);
            }

            return false;
        }

        return array('responce_data'=>$data,'request_data'=>$productData);
    }

    public function updateProductInClyde($sku,$product, $id , $errorReturn = false)
    {
        $productData = $this->getProductClydeData($product);
        $data = $this->_clydeApi->getProductUpdate($sku, $productData, $id);
        if(isset($data['errors'])){
            $value = $data['errors'];
            if(isset($value[0]['code'])){
                $detail = isset($value[0]['detail'])?$value[0]['detail']:'';
                $title = isset($value[0]['title'])?$value[0]['title']:'';
                return array('errors'=>$sku.' '.$title.', '.$detail);
            }
            
            return false;
        }

        return array('responce_data'=>$data,'request_data'=>$productData);
    }
}