<?php
namespace Clyde\Warranty\Model\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderShipmentAfter implements ObserverInterface
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
        $this->_warrantysale = $warrantysale;
        $this->_helper = $helper;
        $this->messageManager = $messageManager;
        $this->_apiClyde = $apiClyde;
        $this->_orderManager = $orderManager; 

    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //observer to process clyde shipment
        if($this->_helper->getEnableModule() == 1){
            $shipment = $observer->getEvent()->getShipment();
            $order = $shipment->getOrder();
            $finalarray = $this->_apiClyde->setClydedata($shipment);

            $orderId = $order->getIncrementId();
            if(!empty($finalarray['errors'])){
                $warrantysaledatafal = $this->_warrantysale->create();
                $warrantysaledatafal->addData(
                    array(
                    "order_id" => $order->getIncrementId(),
                    "shipment_id" => $shipment->getIncrementId(),
                    "contract_sale_id" => '',
                    "status" => 'Failure',
                    "processed_itemids" => json_encode(array()),
                    "status_comment" => ''.json_encode($finalarray['errors'][0]),
                    )
                );
                $orderData = $this->_orderManager->load($order->getId());
                $commentMessage = "Clyde Error Message: ".$finalarray['errors'][0]['code'].$finalarray['errors'][0]['title'].' - '.$finalarray['errors'][0]['detail'];;
                $orderData->addStatusHistoryComment($commentMessage);
                $orderData->save();
                $this->messageManager->addError($commentMessage);
                $warrantysaledatafal->save();
            }

            if(!empty($finalarray['data'])){
                $warrantysaledata = $this->_warrantysale->create();
                $contactSaleId = isset($finalarray['data']['attributes']['contractSales'][0]['id'])?$finalarray['data']['attributes']['contractSales'][0]['id']:'';
                $warrantysaledata->addData(
                    array(
                    "order_id" => $order->getIncrementId(),
                    "shipment_id" => $shipment->getIncrementId(),
                    "contract_sale_id" => $contactSaleId ,
                    "status" => ($contactSaleId != '')?'Success':'Failure',
                    "status_comment" => isset($finalarray['data']['attributes']['contractSales'][0]['id'])?'<strong>Contract Active: </strong>'.date('Y-m-d'):'',
                    "processed_itemids" => json_encode($finalarray['processed']),
                    "contract_date"=>date('Y-m-d H:i:s')
                    )
                );
               
                $orderData = $this->_orderManager->load($order->getId());
                if($contactSaleId != ''){
                    $orderData->addStatusHistoryComment('The Clyde contract is created successfully<br/>Your Contract id is : '.$contactSaleId);
                }else{
                    $this->messageManager->addError(__('Some thing wrong to create contract sale, contract id not return'));
                }

                $orderData->save();
                $warrantysaledata->save();
            }
        }
    }
}