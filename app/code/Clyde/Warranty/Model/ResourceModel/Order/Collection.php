<?php
namespace Clyde\Warranty\Model\ResourceModel\Order;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'order_sync_id';
    
    protected function _construct()
    {
        $this->_init('Clyde\Warranty\Model\Order', 'Clyde\Warranty\Model\ResourceModel\Order');
    }
}
