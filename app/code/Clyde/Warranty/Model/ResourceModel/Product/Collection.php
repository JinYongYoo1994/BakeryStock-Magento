<?php
namespace Clyde\Warranty\Model\ResourceModel\Product;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'items_id';
    
    protected function _construct()
    {
        $this->_init('Clyde\Warranty\Model\Product', 'Clyde\Warranty\Model\ResourceModel\Product');
    }
}
