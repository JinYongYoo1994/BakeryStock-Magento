<?php
namespace Clyde\Warranty\Model\ResourceModel;
 
class Warranty extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    protected $_idFieldName = 'warranty_id';
   
    protected function _construct()
    {
        $this->_init('clyde_warranty', 'warranty_id');
    }
    
    public function getWarrantryByPrice($price)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'warranty_id')
            ->where('product_price_from <= :product_price_from')
            ->where('product_price_to >= :product_price_to')
            ->where("(((DATE(warranty_start) <= :warranty_start) OR (warranty_start IS NULL))) AND (((DATE(warranty_end) >= :warranty_end) OR (warranty_end IS NULL))) OR enable_by_range = :enable_by_range")
            ->where('status = :status');
        $binds = array('product_price_from' => $price, 'product_price_to' => $price, 'warranty_start'=> date('Y-m-d'),'warranty_end'=>date('Y-m-d'), 'status' => \Clyde\Warranty\Model\Status::ENABLED, 'enable_by_range' => \Clyde\Warranty\Model\Status::DISABLED );
        $result = $adapter->fetchAll($select, $binds);
        $result1 = $this->getWarrantryHighestPrice($price);
        $result = array_merge($result, $result1);
        return array_column($result, 'warranty_id');
    }
    
    public function getWarrantryHighestPrice($price)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'warranty_id')
            ->where('product_price_from <= :product_price_from')
            ->where('product_price_to = :product_price_to')
            ->where("(((DATE(warranty_start) <= :warranty_start) OR (warranty_start IS NULL))) AND (((DATE(warranty_end) >= :warranty_end) OR (warranty_end IS NULL))) OR enable_by_range = :enable_by_range")
            ->where('status = :status');
        $binds = array('product_price_from' => $price, 'product_price_to' => 0.0, 'warranty_start'=> date('Y-m-d'),'warranty_end'=>date('Y-m-d'), 'status' => \Clyde\Warranty\Model\Status::ENABLED, 'enable_by_range' => \Clyde\Warranty\Model\Status::DISABLED);
        return $adapter->fetchAll($select, $binds);
    }
}
