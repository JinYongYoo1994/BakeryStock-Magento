<?php
namespace Clyde\Warranty\Controller\Adminhtml\Customer;

class Index extends \Magento\Customer\Controller\Adminhtml\Index
{
    public function execute()
    {
        $customerId = $this->initCurrentCustomer();
        $model = $this->_objectManager->create('Clyde\Warranty\Model\Customerwarranty');
        $statistic = $model->getStatistic($customerId);
        $this->_coreRegistry->register('customerwarranty_data', $statistic);
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}