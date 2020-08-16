<?php 
namespace Clyde\Warranty\Model\Api;
use Clyde\Warranty\Api\SalesWarrantyInterface;
 
class SalesWarranty implements SalesWarrantyInterface
{

  protected $_resourceConnection;

  public function __construct(
      \Magento\Framework\App\ResourceConnection $ResourceConnection 
  ) {
    $this->_resourceConnection = $ResourceConnection;
  }

  /**
  * {@inheritdoc}
  */
  public function getSalesData($status, $fromDate = null, $toDate = null)
  {
    $data = array();
    if($status != ''){
      $conn = $this->_resourceConnection->getConnection();
      $select = $conn->select()
        ->from($conn->getTableName('warranty_sales'), '*')
        ->join(array('order' =>$conn->getTableName('sales_order')), 'order.increment_id = warranty_sales.order_id', array('order.created_at','order.created_at'));
      if($status){
        $select->where('LOWER(warranty_sales.status) = ?', strtolower($status));
      }

      if($fromDate){
        $select->where('order.created_at>=?', $fromDate);
      }

      if($toDate){
        $select->where('order.created_at<=?', $toDate);
      }

      $result = $conn->fetchAll($select);
      if(count($result) > 0){
        $data = $result;
      }else{
        $data[] = array('message'=>(string)__('Data not found'));
      }
    }else{
      $data[] = array('error'=>(string)__('Status filed is required'));
    }
    
    return $data ;
  }
}
