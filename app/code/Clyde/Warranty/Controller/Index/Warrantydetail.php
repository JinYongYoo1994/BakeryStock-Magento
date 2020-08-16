<?php
namespace Clyde\Warranty\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\Controller\ResultFactory;

class Warrantydetail extends \Magento\Framework\App\Action\Action
{
   
     protected $_product;

    protected $_resultPageFactory;

    protected $_helper;

    protected $_coreSession;

    protected $_productObj;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Clyde\Warranty\Model\Product $productObj
    ) {
        parent::__construct($context);
        $this->_product = $product;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        $this->_coreSession = $coreSession;
        $this->_productObj = $productObj;
    }
    
    public function execute()
    {
        $response = array();    
        $resultPage = $this->_resultPageFactory->create();
        $data = (array)$this->getRequest()->getPost();
        if (isset($data['super_attribute']) != '') {
            $arrayKeys = array_keys($data['super_attribute']);
            $lastArrayKey = array_pop($arrayKeys);
            if (isset($data['product_type']) && $data['product_type'] == 'configurable' && isset($data['super_attribute']) != '') {
                    $data['product'] = $this->_productObj->getConfAssoProductId($data['product'], $data['super_attribute']);
            }
        }
        
        $response = $data;
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response);
        return $resultJson;
    }

}
