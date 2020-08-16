<?php
namespace Clyde\Warranty\Model\ResourceModel\Customerwarranty;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected $_idFieldName = 'customerwarranty_id';
    
    protected function _construct()
    {
        $this->_init('Clyde\Warranty\Model\Customerwarranty', 'Clyde\Warranty\Model\ResourceModel\Customerwarranty');
    }
    public function addCustomerIdFilter($customerId)
    {
        $this->getSelect()
        ->join(
            array('sales_item' => $this->getTable('sales_order_item')),
            'main_table.item_id = sales_item.item_id',
            array('name','sku','warranty_info')
        )->join(
            array('sales_order' => $this->getTable('sales_order')),
            'sales_item.order_id = sales_order.entity_id',
            array('increment_id')
        )->where(
            'main_table.customer_id = ?',
            $customerId
        )->order('customerwarranty_id DESC');
        return $this;
    }
}
