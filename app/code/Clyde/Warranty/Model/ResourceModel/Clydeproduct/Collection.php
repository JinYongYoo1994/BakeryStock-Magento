<?php
namespace Clyde\Warranty\Model\ResourceModel\Clydeproduct;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'product_sync_id';
    
    protected function _construct()
    {
        $this->_init('Clyde\Warranty\Model\Clydeproduct', 'Clyde\Warranty\Model\ResourceModel\Clydeproduct');
    }
}
