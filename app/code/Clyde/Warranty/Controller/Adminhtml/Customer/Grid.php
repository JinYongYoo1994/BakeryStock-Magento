<?php
namespace Clyde\Warranty\Controller\Adminhtml\Customer;

class Grid extends \Magento\Customer\Controller\Adminhtml\Index
{
    public function execute()
    {
        $this->initCurrentCustomer();
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}