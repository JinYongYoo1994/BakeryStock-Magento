<?php
namespace Clyde\Warranty\Controller\Index;

use Magento\Customer\Model\Session;

class Index extends \Magento\Framework\App\Action\Action
{
   
    protected $_cacheTypeList;

    protected $_cacheState;

    protected $_cacheFrontendPool;

    protected $resultPageFactory;

    protected $_customerSession;

    protected $_helper;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Clyde\Warranty\Helper\Data $helper,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
    }
   
    public function execute()
    {
        if ($this->_helper->getEnableModule()) {
            $this->resultPage = $this->resultPageFactory->create();
            $this->resultPage->getConfig()->getTitle()->prepend(__('Warranties & Garantees'));
            return $this->resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('home');
            return $resultRedirect;
        }
    }
}
