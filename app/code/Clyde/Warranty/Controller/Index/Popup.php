<?php
namespace Clyde\Warranty\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Popup extends \Magento\Framework\App\Action\Action
{
    protected $_product;

    protected $_resultPageFactory;

    protected $_helper;

    protected $_coreSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        parent::__construct($context);
        $this->_product = $product;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        $this->_coreSession = $coreSession;
    }
    
    public function execute()
    {
        $response = array();    
        $resultPage = $this->_resultPageFactory->create();
        $data = (array)$this->getRequest()->getPost();
        if (isset($data['product']) && $data['product'] != '') {
            $_product = $this->_product->load($data['product']);
            $productData = $_product->getData();
            $response['product_sku'] = $productData['sku'];
            $response['success'] = 1;
            $checkCondition = $this->checkCondition();

            if ($checkCondition === true || $checkCondition == 1) {
                $response['show'] = 1;
            }
        } else {
            $response['error'] = 1;
            $response['show'] = 0;
            $response['message'] = __('Product not found');
        }
        
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response);
        return $resultJson;
    }

    public function checkCondition()
    {
        if ($this->_helper->getAddtocartPopup() == \Clyde\Warranty\Model\Warranty\Addtocartpopup::SHOW_POPUP) {
            return true;
        } elseif ($this->_helper->getAddtocartPopup() == \Clyde\Warranty\Model\Warranty\Addtocartpopup::NO_PLAN_POPUP) {
            $this->_coreSession->start();
            $value = $this->_coreSession->getWarrantyApply();
            $this->_coreSession->unsWarrantyApply();
            return $value;
        } elseif($this->_helper->getAddtocartPopup() == \Clyde\Warranty\Model\Warranty\Addtocartpopup::HIDE_POPUP) {
            return false;
        }
    }
}
