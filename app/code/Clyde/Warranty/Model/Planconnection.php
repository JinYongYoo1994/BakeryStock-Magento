<?php
namespace Clyde\Warranty\Model;
 
class Planconnection extends \Magento\Framework\Model\AbstractModel
{
    protected $_storeManager;
    
    protected $_countryFactory;
    
    protected $messageManager;
    
    
    protected $_transportBuilder = '';
    
    protected function _construct()
    {
        $this->_init('Clyde\Warranty\Model\ResourceModel\Planconnection');
    }
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $store,
        array $data = array()
    ) {
        $this->_storeManager = $store;
        parent::__construct($context, $registry);
    }
    
    public function setWarrantyPlan($id ,$data)
    {
       if(isset($data['category_products_plan']) && $data['category_products_plan'] != ''){
            $decode = json_decode($data['category_products_plan'], true);
            $plans = array_keys($decode);
            $data = $this->getDeleteWarrantyPlan($id);
            $data = $this->getInsertWarrantyPlan($id, $plans);
            return $data;
       }
       
    }
    
    public function setProductWarranty($productId , $warranties)
    {
       if(empty($productId) !== true && empty($warranties) !== true){
            $data = $this->_getResource()->getDeleteWarrantyProduct($productId, 'product_id');
            $data = $this->_getResource()->getInsertProductWarranty($productId, $warranties);
            return $data;
       }
       
    }
    
    public function getInsertWarrantyPlan($id, $plans)
    {
        return $this->_getResource()->getInsertWarrantyPlan($id, $plans);
    }
    
    public function getWarrantyProduct($warranty_id, $product_id = null)
    {
        return $this->_getResource()->getWarrantyProduct($warranty_id, $product_id);
    }
    
    public function assignToWarranty($id, $plans)
    {
        $warranty = $this->getDeleteWarrantyPlan($id);
        return $this->_getResource()->getInsertWarrantyPlan($id, $plans);
    }

    public function getDeleteWarrantyPlan($id)
    {
        return $this->_getResource()->getDeleteWarrantyPlan($id, 'warranty_id');
    }
    
}