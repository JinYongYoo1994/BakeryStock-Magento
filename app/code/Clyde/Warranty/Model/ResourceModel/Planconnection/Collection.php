<?php
namespace Clyde\Warranty\Model\ResourceModel\Planconnection;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'items_id';
    
    protected function _construct()
    {
        $this->_init('Clyde\Warranty\Model\Planconnection', 'Clyde\Warranty\Model\ResourceModel\Planconnection');
    }
}
