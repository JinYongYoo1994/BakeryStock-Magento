<?php 
namespace Clyde\Warranty\Model\Api;
use Clyde\Warranty\Api\SalesWarrantyContractInterface;
 
class SalesClydeContract implements SalesWarrantyContractInterface
{

    protected $_resourceConnection;
  protected $_order;
  protected $_shipment;
  protected $_apiClyde;
  protected $_warrantysale;
  protected $_helper;
  protected $_processedData = array();

  public function __construct(
      \Magento\Framework\App\ResourceConnection $ResourceConnection,
      \Magento\Sales\Model\OrderFactory $order,
      \Clyde\Warranty\Model\WarrantysaleFactory $warrantysale,
      \Clyde\Warranty\Helper\Data $helper,
      \Magento\Sales\Model\Order\Shipment $shipment,
      \Clyde\Warranty\Model\Api\Clyde $apiClyde 
  ) {
      $this->_resourceConnection = $ResourceConnection;
      $this->_apiClyde = $apiClyde;
      $this->_order = $order;
      $this->_shipment = $shipment;
      $this->_warrantysale = $warrantysale;
      $this->_helper = $helper;
  }

    /**
     * {@inheritdoc}
     */
    public function getClydeContract($id)
    {
    $data = array();
    if($id != ''){
      $conn = $this->_resourceConnection->getConnection();
      $select = $conn->select()
                ->from($conn->getTableName('warranty_sales'), '*');
      if($id){
        $select->where('warranty_sales.order_id = ?', $id);
      }

      $result = $conn->fetchAll($select);
      if(count($result) > 0){
        $data = $result;
      }else{
        $data[] = array('message'=>(string)__('Data not found'));
      }
    }else{
      $data[] = array('error'=>(string)__('Id filed is required'));
    }

    return $data ;
    }

  public function getFailureData()
  {
    $conn = $this->_resourceConnection->getConnection();
    $select = $conn->select()
      ->from($conn->getTableName('warranty_sales'), '*')
      ->join(array('order' =>$conn->getTableName('sales_order')), 'order.increment_id = warranty_sales.order_id', array('order.created_at','order.created_at'));
    $select->where('LOWER(warranty_sales.status) = ?', strtolower('Failure'));
    $result = $conn->fetchAll($select);
    return $result;
  }

  public function getContractRetry()
  {
    $allData = $this->getFailureData();
    $sorder_ids = array();
    if(count($allData) > 0){
      foreach($allData as $key=>$value){
        $sorder_ids[] = $value['shipment_id'];
      }
    }

    if(count($sorder_ids) > 0){
      $shipmentollection = $this->_order->create();
      //$collection = $shipmentollection->getCollection()->addFieldToFilter('increment_id', array('in'=>$sorder_ids));
      $collection = $this->_shipment->getCollection();
      $collection->addFieldToFilter('increment_id', array('in'=>$sorder_ids));
      foreach($collection as $ship){
        $this->getContractRetryForFailure($ship);
      }
    }

    return $this->_processedData;
  }

  public function getContractRetryForFailure($order)
  {
        $orderId = $order->getIncrementId();

        $finalarray = $this->_apiClyde->setClydedata($order);
        
            if(!empty($finalarray['errors'])){ 
                $warrantysaledatafal = $this->_warrantysale->create();
                $collection = $warrantysaledatafal->getCollection()->addFieldToFilter('shipment_id', $orderId);
                foreach($collection as $row){
                    $row->setData("contract_sale_id", "");
                    $row->setData("status", "Failure");
                    $row->setData("status_comment", json_encode($finalarray['errors'][0], true));
                    $row->setData("processed_itemids", json_encode(array()));
                    $row->save();
                }

                $orderData = $order->getOrder();
                $commentMessage = 'Clyde Error: '.$finalarray['errors'][0]['title'].' - '.$finalarray['errors'][0]['detail'];
                $orderData->addStatusHistoryComment($commentMessage);
                $orderData->save();
                $this->_processedData[$orderId] = array('error'=>$commentMessage);
            }

            if(!empty($finalarray['data'])){
                $warrantysaledata = $this->_warrantysale->create(); 
                $newcollection = $warrantysaledata->getCollection()->addFieldToFilter("shipment_id", $orderId);
                foreach($newcollection as $newrow){
                    $newrow->setData("contract_sale_id", $finalarray['data']['attributes']['contractSales'][0]['id']);
                    $newrow->setData("status", "Success");
                    $newrow->setData("status_comment", "");
                    $newrow->setData("processed_itemids", json_encode($finalarray['processed']));
                    $newrow->save();
                }

                $orderData = $order->getOrder();
                $orderData->addStatusHistoryComment('The Clyde contract was created successfully<br/>Your Contract id is'.$finalarray['data']['attributes']['contractSales'][0]['id']);
                $orderData->save();
                $this->messageManager->addSuccess('The Clyde contract was created successfully.');
                $this->_processedData[$orderId] = array('success'=>'The Clyde contract was created successfully.');
            }

    
    return $this->_processedData ;
  }
}
