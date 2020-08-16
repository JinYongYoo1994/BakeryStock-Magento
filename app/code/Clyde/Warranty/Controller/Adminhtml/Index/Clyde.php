<?php
namespace Clyde\Warranty\Controller\Adminhtml\Index;
 
class Clyde extends \Magento\Backend\App\Action
{
    
    protected $_resultPageFactory;
    protected $_warrantysale;
    protected $_helper;
    protected $messageManager;
    protected $_orderManager;
    protected $_shipment;
    protected $_apiClyde;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Clyde\Warranty\Model\WarrantysaleFactory $warrantysale,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Model\Order $orderManager,
        \Clyde\Warranty\Model\Api\Clyde $apiClyde,
        \Magento\Sales\Model\Order\Shipment $shipment
    ) { 
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_warrantysale = $warrantysale;
        $this->_helper = $helper;
        $this->messageManager = $messageManager;
        $this->_orderManager = $orderManager;
        $this->_apiClyde = $apiClyde;
        $this->_shipment = $shipment;
    }
    
    public function execute()
    {
        $shipmentId = $this->getRequest()->getParam('id');
        $returnId = $this->getRequest()->getParam('return_url');
        $shipmentCollection = $this->_shipment->load($shipmentId);
        $order = $shipmentCollection->getOrder();
        $orderId = $shipmentCollection->getIncrementId();
        $finalarray = $this->_apiClyde->setClydedata($shipmentCollection);
        $resultRedirect = $this->resultRedirectFactory->create();

            if (!empty($finalarray['errors'])) { 
                $warrantysaledatafal = $this->_warrantysale->create();
                $collection = $warrantysaledatafal->getCollection()->addFieldToFilter('shipment_id', $orderId);
                foreach($collection as $row){
                    $row->setData("contract_sale_id", "");
                    $row->setData("status", "Failure");
                    $row->setData("status_comment", json_encode($finalarray['errors'][0], true));
                    $row->save();
                }

                $orderData = $this->_orderManager->load($order->getId());
                $commentMessage = "Clyde Error Message: ".$finalarray['errors'][0]['code'].' - '.$finalarray['errors'][0]['title'].' - '.$finalarray['errors'][0]['detail'];;
                $orderData->addStatusHistoryComment($commentMessage);
                $orderData->save();
                $this->messageManager->addError($commentMessage);
            }

            if (!empty($finalarray['data'])) {
                $warrantysaledata = $this->_warrantysale->create(); 
                $newcollection = $warrantysaledata->getCollection()->addFieldToFilter("shipment_id", $orderId);
                $contactSaleId = isset($finalarray['data']['attributes']['contractSales'][0]['id'])?$finalarray['data']['attributes']['contractSales'][0]['id']:'';
                foreach ($newcollection as $newrow) {
                    $status = ($contactSaleId != '')?'Success':'Failure';
                    $newrow->setData("contract_sale_id", $contactSaleId);
                    $newrow->setData("status", $status);
                    $newrow->setData("status_comment", "");
                    $newrow->save();
                }

                $orderData = $this->_orderManager->load($order->getId());
                if($contactSaleId != ''){
                    $orderData->addStatusHistoryComment('The Clyde contract was created successfully<br/>Your Contract id is : '.$contactSaleId);
                }else{
                    $this->messageManager->addError(__('Something went wrong with the Clyde sale, contract id was not return'));
                }
                $orderData->save();
            }

           if ($returnId==1) {
            return $resultRedirect->setPath('sales/shipment/view', array('shipment_id' => $shipmentCollection->getId()));
           } else {
            return $resultRedirect->setPath('warranty/warrantysale/index');
           }
           
       
    }
     
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Clyde_Warranty::manage');
    }
}
