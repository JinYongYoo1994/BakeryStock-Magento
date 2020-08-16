<?php
namespace Clyde\Warranty\Controller\Adminhtml\Sync;

use Magento\Framework\Controller\ResultFactory;

class Sync extends \Magento\Backend\App\Action
{
    
    protected $_resultPageFactory;

    protected $_ProductsyncModel;
    protected $_productsyncClydeModel;

    protected $registryObject;

    protected $sessionData;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Clyde\Warranty\Model\Productsync $ProductsyncModel,
        \Clyde\Warranty\Model\ProductSyncToClyde $productsyncClydeModel,
        \Magento\Framework\Registry $registryObject
    ) { 
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_ProductsyncModel = $ProductsyncModel;
        $this->_productsyncClydeModel = $productsyncClydeModel;
        $this->registryObject = $registryObject;
        $this->sessionData = $context->getSession();
    }
    
    public function execute()
    {
        try {
            $data = (array)$this->getRequest()->getPost();
            // $result = $this->_ProductsyncModel->getSyncProduct($data);
            //$page = $data['product_limit_page'];
            //$result = $this->_ProductsyncModel->getSyncData($page);
            $result = $this->_productsyncClydeModel->getSyncProduct($data);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($result);
            return $resultJson;
        } catch (\Exception $e) {
            $responce = array('error' => 1 , 'message' => $e->getMessage() , 'stop' => 1);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($responce);
            return $resultJson;
        }
    }
    
}
