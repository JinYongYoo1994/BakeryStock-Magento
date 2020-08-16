<?php
namespace Clyde\Warranty\Model\ResourceModel;
 
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    protected $_idFieldName = 'items_id';
   
    protected function _construct()
    {
        $this->_init('clyde_warranty_products', 'items_id');
    }

    public function getWarrantyProduct($id, $product_id = null, $condition_rule_type = null)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'product_id')
            ->where('warranty_id = :warranty_id');
        $binds['warranty_id'] = (int)$id;
        if (empty($product_id) !== true) {
            $select->where('product_id = :product_id');
            $binds['product_id'] = (int)$product_id;
        }

        if (empty($condition_rule_type) !== true) {
            $select->where('condition_rule_type = :condition_rule_type');
            $binds['condition_rule_type'] = (int)$condition_rule_type;
        }

        $result = $adapter->fetchAll($select, $binds);
        if (empty($result) !== true) {
             return array_column($result, 'product_id');
        }

        return $result;
       
    }

    public function getInsertWarrantyProduct($id ,$products , $condition_rule_type = null)
    {
        
        if (empty($products) !== true) {
            $rows = array();
            foreach($products as $product) {
                $rows[] = array('warranty_id'=>$id,'product_id'=>$product, 'condition_rule_type'=>$condition_rule_type);
            }

            $adapter = $this->getConnection();

            return $adapter->insertMultiple($this->getMainTable(), $rows);
        }
        
    }

    public function getInsertProductWarranty($productId ,$warranties)
    {
        if (empty($warranties) !== true) {
            $rows = array();
            foreach ($warranties as $warranty) {
                $rows[] = array('warranty_id'=>$warranty,'product_id'=>$productId);
            }

            $adapter = $this->getConnection();
            return $adapter->insertMultiple($this->getMainTable(), $rows);
        }
        
    }

    public function getDeleteWarrantyProduct($id , $field = 'warranty_id')
    {
        $adapter = $this->getConnection();
        $where = array($adapter->quoteInto($field.' =?', $id));
        return $adapter->delete($this->getMainTable(), $where);
    }
}
