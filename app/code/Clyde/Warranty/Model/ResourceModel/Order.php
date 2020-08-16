<?php
namespace Clyde\Warranty\Model\ResourceModel;

class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

       protected $_idFieldName = 'order_sync_id';

    protected function _construct()
    {
        $this->_init('clyde_order_sync', 'order_sync_id');
    }

    public function getSyncProductBySku($order_increment_id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), '*')
            ->where('order_increment_id = :order_increment_id');
        $binds['order_increment_id'] = $sku;
        $result = $adapter->fetchAll($select, $binds);     
        return $result;
       
    }

    public function getSyncOrder($order_id, $shipment_increment_id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), '*')
            ->where('order_id = ?', $order_id)
            ->where('shipment_increment_id = ?', $shipment_increment_id);
        //$binds['order_increment_id'] = $shipment_increment_id;
        //$binds['order_id'] = $order_id;
        $result = $adapter->fetchAll($select);     
        return $result;
       
    }

    public function getInsertSyncOrder($orders)
    {
        if (empty($orders) !== true) {
            $adapter = $this->getConnection();
            return $adapter->insertMultiple($this->getMainTable(), $orders);
        }
        
    }

    public function getUpdateSyncOrder($id , $field = 'order_increment_id' , $data)
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

    public function getOrderDataInTable($shipment, $order_id, $shipmentData)
    {
        
        $orderData['order_id'] = $order_id;
        $orderData['clyde_order_json'] = json_encode($shipmentData, JSON_UNESCAPED_SLASHES);
        $orderData['shipment_increment_id'] = $shipment;
        $orderData['created_time'] = date('Y-m-d H:i:s');
        $orderData['updated_time'] = date('Y-m-d H:i:s');
        $this->getInsertSyncOrder($orderData);
    }

    public function getOrders($type) 
    {
        $connection = $this->getConnection();
        $item_table = $connection->getTableName('sales_order_item'); 
        $order_table = $connection->getTableName('sales_order'); 
        $order_address = $connection->getTableName('sales_order_address'); 
        $select = $connection->select()
            ->from(array('main' =>$item_table), array('sku','price','order_id','item_id'))
            ->join(array('order' =>$order_table), 'main.order_id = order.entity_id', array('increment_id'))
            ->join(array('address' =>$order_address), 'main.order_id = address.parent_id', array('firstname','lastname','email','telephone','street','city','postcode','country_id','region'))
            //->where('main.warranty_info IS NULL')
            ->where('address.address_type = ?', 'shipping')
            ->where('main.product_type IN (?)', $type);
        return $connection->fetchAll($select);

    }

    public function getShipments($type) 
    {
        $connection = $this->getConnection();
        $shipment_item = $connection->getTableName('sales_shipment_item'); 
        $order_item = $connection->getTableName('sales_order_item'); 
        $sales_shipment = $connection->getTableName('sales_shipment'); 
        $order_address = $connection->getTableName('sales_order_address'); 
        $select = $connection->select()
            ->from(array('main' =>$shipment_item), array('sku','price','item_id'=>'order_item_id','qty'))
            ->join(array('order_item' =>$order_item), 'main.order_item_id = order_item.item_id', array('order_item.warranty_info','order_item.product_type'))
            ->join(array('sales_shipment' =>$sales_shipment), 'main.parent_id = sales_shipment.entity_id', array('increment_id','    order_id'))
            ->join(array('address' =>$order_address), 'sales_shipment.order_id = address.parent_id', array('firstname','lastname','email','telephone','street','city','postcode','country_id','region'))
            ->where('Length(order_item.warranty_info) < 4')
            ->where('address.address_type = ?', 'shipping')
            ->where('order_item.product_type IN (?)', $type);
        return $connection->fetchAll($select);

    }

    public function addCronSyncToschedule()
    {
        $data['job_code'] = 'clyde_order_sync';
        $data['status'] = 'pending';
        $endTime = strtotime("+4 minutes", strtotime(date('Y-m-d H:i:s')));
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['scheduled_at'] = date('Y-m-d H:i:s', $endTime);
        $adapter = $this->getConnection();
        $tableName = $adapter->getTableName('cron_schedule');
        return $adapter->insert($tableName, $data);
        
    }

}
