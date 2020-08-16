<?php
namespace Clyde\Warranty\Model\ResourceModel;
 
class Customerwarranty extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    protected $_idFieldName = 'customerwarranty_id';
   
    protected function _construct()
    {
        $this->_init('clyde_customerwarranty', 'customerwarranty_id');
    }
   
    public function getWarrantryByPrice($price)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), '*')
            ->where('product_price_from <= :product_price_from')
            ->where('product_price_to >= :product_price_to')
            ->where('status = :status');
        $binds = array('product_price_from' => $price,
                  'product_price_to' => $price,
                  'status' => \Clyde\Warranty\Model\Status::ENABLED
                  );
        return $adapter->fetchRow($select, $binds);
    }
}
