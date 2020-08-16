<?php
namespace Clyde\Warranty\Model\ResourceModel\Customerwarranty\Collection;

use Magento\Customer\Controller\RegistryConstants as RegistryConstants;

class Grid extends \Clyde\Warranty\Model\ResourceModel\Customerwarranty\Collection
{
    protected $_registryManager;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Clyde\Warranty\Model\ResourceModel\Customerwarranty $resource,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->_registryManager = $registry;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }
    
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerIdFilter($this->_registryManager->registry(RegistryConstants::CURRENT_CUSTOMER_ID));
        return $this;
    }
}
