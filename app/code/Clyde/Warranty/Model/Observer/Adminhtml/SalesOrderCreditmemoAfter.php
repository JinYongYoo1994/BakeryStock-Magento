<?php
namespace Clyde\Warranty\Model\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderCreditmemoAfter implements ObserverInterface
{
    protected $_warrantysale;

    protected $_helper;

    protected $messageManager;

    protected $_orderManager;

    protected $_apiClyde;

    public function __construct(
        \Clyde\Warranty\Model\WarrantysaleFactory $warrantysale,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Clyde\Warranty\Model\Api\Clyde $apiClyde,
        \Magento\Sales\Model\Order $orderManager
    ) {
        $this->_apiClyde = $apiClyde;
        $this->_warrantysale = $warrantysale;
        $this->_helper = $helper;
        $this->messageManager = $messageManager;
        $this->_orderManager = $orderManager;

    }   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->_helper->getEnableModule() == 1){
            $creditmemo = $observer->getEvent()->getCreditmemo();
            $order = $creditmemo->getOrder();
            $orderId = $order->getIncrementId();
            $warrantysaledata = $this->_warrantysale->create(); 
            $collection = $warrantysaledata->getCollection()->addFieldToFilter("order_id", $orderId);
            if(count($collection)>0){
                foreach($collection as $row){
                    $contractid = $row['contract_sale_id'];
                    $entity_id = $row['id'];
                }
                $this->_apiClyde->setMessageManagerClyde($this->messageManager);
                $result = $this->_apiClyde->setContractSales($contractid, true);
                if(isset($result['errors'])){
                    $data = $result['errors'];
                    if(isset($data[0]['code']) && isset($data[0]['status']) && $data[0]['status'] == '204'){
                        if(isset($entity_id) && !empty($entity_id)){
                            $warrantysaledata->load($entity_id);
                            $contract_date = $warrantysaledata->getStatusComment();
                            $warrantysaledata->setStatusComment($contract_date.'<br/><strong>Refunded Date: </strong>'.date('Y-m-d H:i:s'));
                            $warrantysaledata->setRefundedDate(date('Y-m-d H:i:s'));
                            $warrantysaledata->setStatus('Cancel');
                            $warrantysaledata->setRefunded(1);
                            $warrantysaledata->save();
                        }

                       $this->messageManager->addSuccess("The Clyde warranty return request was successfully created.");
                    }
                }else{
                    $this->messageManager->addError("There is something wrong with the Clyde Warranty contact id.");
                }
            }
        }
    }
    
}
