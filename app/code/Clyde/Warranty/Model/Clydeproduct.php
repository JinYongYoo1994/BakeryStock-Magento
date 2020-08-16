<?php
namespace Clyde\Warranty\Model;
 
class Clydeproduct extends \Magento\Framework\Model\AbstractModel
{
    
    protected function _construct()
    {
        $this->_init('Clyde\Warranty\Model\ResourceModel\Clydeproduct');
    }
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        parent::__construct($context, $registry);
    }
    
    public function getInsertSyncProduct($products)
    {
        return $this->_getResource()->getInsertSyncProduct($products);
    }
    
    public function getUpdateSyncProduct($id, $products)
    {
        return $this->_getResource()->getUpdateSyncProduct($id, $products);
    }
    
    public function getDeleteSyncProduct($id)
    {
        return $this->_getResource()->getDeleteSyncProduct($id);
    }

    public function getProductDataInTable($sku, $products)
    {
        return $this->_getResource()->getProductDataInTable($sku, $products);
    }

    public function addCronSyncToschedule()
    {
        return $this->_getResource()->addCronSyncToschedule();
    }

    public function checkProductDetailSame($sku, $productData)
    {
        $data = $this->_getResource()->getSyncProductBySku($sku);
        if(count($data) > 0){
            $requestData = json_encode($productData, JSON_UNESCAPED_SLASHES);
            $result = $data[0];
            $resultOfValue = json_decode($result['clyde_product_json'], true);
            $existsData = json_encode($resultOfValue, JSON_UNESCAPED_SLASHES);
            if(strlen($requestData) == strlen($existsData)){
                return true;
            }
        }

        return false;
    }
    
}