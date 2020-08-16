<?php
namespace Clyde\Warranty\Controller\Adminhtml\Products;

class Search extends \Magento\Backend\App\Action
{
    protected $_helper;

    protected $_productloader;

    protected $_warrantyFactory;

    protected $_productFactory;

    protected $resultPageFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Catalog\Model\Product $_productloader,
        \Clyde\Warranty\Model\Warranty $warrantyFactory,
        \Clyde\Warranty\Model\Product $productFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_productloader = $_productloader;
        $this->_warrantyFactory = $warrantyFactory;
        $this->_productFactory = $productFactory;
        $this->resultPageFactory = $resultPageFactory;
    }
   
    public function execute()
    {
        $data = (array)$this->getRequest()->getPost();
        $result = array();
        if (isset($data['product_sku']) && $data['product_sku'] != '') {
            $collection = $this->_productloader->getCollection();
            $collection->addFieldToSelect('name');
            $collection->addFieldToSelect('price');
            $collection->addFieldToSelect('final_price');
            $collection->addFieldToFilter('sku', array('like' => $data['product_sku'].'%'));
            $html = $this->getAutoBlock($collection);
            if ($collection->count()) {
                $result = array('total'=>$collection->count(),'success'=>true,'html'=>$html);
            } else {
                $result = array('error'=>true,'message'=>'Product not found');
            }
        }

        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
        $this->getResponse()->sendResponse();
        return false;
    }
    
    public function getAutoBlock($collection)
    {
        $resultPage = $this->resultPageFactory ->create();
        return $resultPage->getLayout()
                ->createBlock('Clyde\Warranty\Block\Adminhtml\Warranty\Products\Autocomplete')
                ->setCollection($collection)
                ->setTemplate('Clyde_Warranty::warranty/products/autocomplete.phtml')
                ->toHtml();
    }
}
