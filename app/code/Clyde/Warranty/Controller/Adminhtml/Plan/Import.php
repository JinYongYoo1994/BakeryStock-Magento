<?php
namespace Clyde\Warranty\Controller\Adminhtml\Plan;

class Import extends \Magento\Backend\App\Action
{

    protected $_resultPageFactory;

    protected $_planModel;

    protected $registryObject;

    protected $sessionData;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Clyde\Warranty\Model\Plan $planModel,
        \Magento\Framework\Registry $registryObject
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_planModel = $planModel;
        $this->registryObject = $registryObject;
        $this->sessionData = $context->getSession();
    }

    public function execute()
    {

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Clyde_Warranty::plan');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Sync Logs'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Clyde_Warranty::plan');
    }
}
