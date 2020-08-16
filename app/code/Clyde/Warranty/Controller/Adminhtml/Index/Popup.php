<?php
namespace Clyde\Warranty\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Popup extends Action
{
    
    protected $_resultPageFactory;

    protected $_warrantyModel;

    protected $registryObject;

    protected $sessionData;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Clyde\Warranty\Model\Warranty $warrantyModel,
        \Magento\Framework\Registry $registryObject
    ) { 
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_warrantyModel = $warrantyModel;
        $this->registryObject = $registryObject;
        $this->sessionData = $context->getSession();
    }
    
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/edit');
    }
     
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Clyde_Warranty::manage');
    }
}
