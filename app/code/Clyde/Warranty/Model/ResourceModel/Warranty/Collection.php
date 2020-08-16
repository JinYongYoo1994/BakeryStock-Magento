<?php
namespace Clyde\Warranty\Model\ResourceModel\Warranty;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected $_idFieldName = 'warranty_id';
    
    protected function _construct()
    {
        $this->_init('Clyde\Warranty\Model\Warranty', 'Clyde\Warranty\Model\ResourceModel\Warranty');
    }
}
