<?php
namespace Clyde\Warranty\Model\ResourceModel;
 
class Clydeproduct extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    protected $_idFieldName = 'product_sync_id';
   
    protected function _construct()
    {
        $this->_init('clyde_product_sync', 'product_sync_id');
    }

    public function getSyncProductBySku($sku)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), '*')
            ->where('sku = :sku');
        $binds['sku'] = $sku;
        $result = $adapter->fetchAll($select, $binds);     
        return $result;
       
    }

    public function getInsertSyncProduct($products)
    {
        if (empty($products) !== true) {
            $adapter = $this->getConnection();
            return $adapter->insertMultiple($this->getMainTable(), $products);
        }
        
    }

    public function addCronSyncToschedule()
    {
        $data['job_code'] = 'clyde_product_sync';
        $data['status'] = 'pending';
        $endTime = strtotime("+2 minutes", strtotime(date('Y-m-d H:i:s')));
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['scheduled_at'] = date('Y-m-d H:i:s', $endTime);
        $adapter = $this->getConnection();
        $tableName = $adapter->getTableName('cron_schedule');
        return $adapter->insert($tableName, $data);
        
    }

    public function getUpdateSyncProduct($id , $field = 'sku' , $data)
    {
        $adapter = $this->getConnection();
        $where = array($adapter->quoteInto($field.' =?', $id));
        return $adapter->update($this->getMainTable(), $data, $where);
    }


    public function getDeleteWarrantyProduct($id , $field = 'warranty_id')
    {
        $adapter = $this->getConnection();
        $where = array($adapter->quoteInto($field.' =?', $id));
        return $adapter->delete($this->getMainTable(), $where);
    }

    public function getProductDataInTable($sku, $products)
    {
        $rows = $this->getSyncProductBySku($sku);
        if(count($rows) > 0){
            $record = $rows[0];
            $products['updated_time'] = date('Y-m-d H:i:s');
            $this->getUpdateSyncProduct($record['product_sync_id'], 'product_sync_id', $products);
        }else{
            $products['created_time'] = date('Y-m-d H:i:s');
            $products['updated_time'] = date('Y-m-d H:i:s');
            $this->getInsertSyncProduct($products);
        }
    }
}
