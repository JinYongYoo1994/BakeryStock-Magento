<?php
namespace Clyde\Warranty\Model\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class ProductSaveEntityAfter implements ObserverInterface
{
    protected $_warrantysale;

    protected $_helper;

    protected $messageManager;

    protected $_orderManager;

    protected $_apiClyde;

    protected $_productSync;

    protected $_clydeproduct;
    

    public function __construct(
        \Clyde\Warranty\Model\WarrantysaleFactory $warrantysale,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Clyde\Warranty\Model\Api\Clyde $apiClyde,
        \Clyde\Warranty\Model\ProductSyncToClyde $productSync,
        \Clyde\Warranty\Model\Clydeproduct $clydeproduct
    ) {
        $this->_warrantysale = $warrantysale;
        $this->_helper = $helper;
        $this->messageManager = $messageManager;
        $this->_apiClyde = $apiClyde;
        $this->_productSync = $productSync;
        $this->_clydeproduct = $clydeproduct;

    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->_helper->getEnableModule() == 1){
            $product = $observer->getProduct();
            if($product->getId()){
                $isClyde = $this->_productSync->getProductByApi($product->getSku());
                if($isClyde === false){
                    $add = $this->_productSync->createProductInClyde($product->getSku(), $product, $product->getId(), true);
                    if(isset($add['errors'])){
                            $this->messageManager->addError('Unable to add Clyde Warranty : '.$add['errors']);
                    }elseif($add !== false){
                        $returnData = $this->_productSync->getClydeProductDataArray($product, json_encode($add['request_data']));
                        $this->_clydeproduct->getProductDataInTable($product->getSku(), $returnData);
                    }
                }else{
                    $productData = $this->_productSync->getProductClydeData($product);
                    $isUpdate = $this->_clydeproduct->checkProductDetailSame($product->getSku(), $productData);
                    if($isUpdate === false){
                        $update = $this->_productSync->updateProductInClyde($product->getSku(), $product, $product->getId(), true);
                        if(isset($update['errors'])){
                            $this->messageManager->addError('Unable to add Clyde Warranty : '.$update['errors']);
                        }elseif($update !== false){
                           $returnData = $this->_productSync->getClydeProductDataArray($product, json_encode($update['request_data']));
                           $this->_clydeproduct->getProductDataInTable($product->getSku(), $returnData);
                           $this->messageManager->addSuccess(__("Updated product successfully in clyde"));
                        }
                    }
                }
            }
        }

    }
}