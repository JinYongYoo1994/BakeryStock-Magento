<?php

namespace Clyde\Warranty\Model\ResourceModel\Warrantysale;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Clyde\Warranty\Model\Warrantysale', 'Clyde\Warranty\Model\ResourceModel\Warrantysale');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
