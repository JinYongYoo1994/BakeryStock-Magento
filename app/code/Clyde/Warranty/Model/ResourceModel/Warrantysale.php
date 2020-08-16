<?php
namespace Clyde\Warranty\Model\ResourceModel;

class Warrantysale extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('warranty_sales', 'id');
    }
}
