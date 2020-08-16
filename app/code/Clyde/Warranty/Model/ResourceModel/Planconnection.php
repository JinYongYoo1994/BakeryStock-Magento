<?php
namespace Clyde\Warranty\Model\ResourceModel;
 
class Planconnection extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    protected $_idFieldName = 'items_id';
   
    protected function _construct()
    {
        $this->_init('clyde_warranty_plan_assign', 'items_id');
    }

    public function getWarrantyPlan($id, $warranty_id = null)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'warranty_id')
            ->where('rule_id = :rule_id');
        $binds['rule_id'] = (int)$id;
        if (empty($warranty_id) !== true) {
            $select->where('warranty_id = :warranty_id');
            $binds['warranty_id'] = (int)$warranty_id;
        }

        $result = $adapter->fetchAll($select, $binds);
        if (empty($result) !== true) {
             return array_column($result, 'warranty_id');
        }

        return $result;
       
    }

    public function getInsertWarrantyPlan($id ,$plans)
    {
        if (empty($plans) !== true) {
            $rows = array();
            foreach($plans as $plan) {
                $rows[] = array('warranty_id'=>$id,'plan_id'=>$plan);
            }

            $adapter = $this->getConnection();
            return $adapter->insertMultiple($this->getMainTable(), $rows);
        }
        
    }

    public function getInsertPlanWarranty($id ,$plans)
    {
        if (empty($plans) !== true) {
            $rows = array();
            foreach ($plans as $plan) {
                $rows[] = array('warranty_id'=>$id,'plan_id'=>$plan);
            }

            $adapter = $this->getConnection();
            return $adapter->insertMultiple($this->getMainTable(), $rows);
        }
        
    }

    public function getDeleteWarrantyPlan($id , $field = 'plan_id')
    {
        $adapter = $this->getConnection();
        $where = array($adapter->quoteInto($field.' =?', $id));
        return $adapter->delete($this->getMainTable(), $where);
    }
}
