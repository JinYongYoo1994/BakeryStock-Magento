<?php
namespace Clyde\Warranty\Model;
use Magento\Framework\App\Filesystem\DirectoryList;
class Order extends \Magento\Framework\Model\AbstractModel
{
    
    CONST ADD_ORDER = 'order_import_sku.csv';
    protected $_apiClyde;
    protected $excelReadXlsx;
    protected $_filesystem;
    protected $_files;
    protected $addedSku;
    protected $importedFiles;
    protected $_dir;
    protected $_planconnection;
    protected $_warranty;
    protected $_allowedProduct;
    protected $_helper;
    protected $addedOrder;
    protected $directory;

    protected function _construct()
    {
        $this->_init('Clyde\Warranty\Model\ResourceModel\Order');
    }
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Clyde\Warranty\Model\Api\Clyde $apiClyde,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $files,
        \Magento\Framework\Filesystem\DirectoryList $dir,   
        array $data = array()
    ) {
        $this->_apiClyde = $apiClyde;
        $this->_filesystem = $filesystem;
        $this->_files = $files;
        $this->_dir = $dir;
        $this->_helper = $helper;
        $this->_allowedProduct = $this->_helper->getProductTypeSync();
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context, $registry);
    }

    public function prepareOrderData($data)
    {
        $orderData = array();
        if (count($data) > 0) {
            foreach ($data as $order) {
                $orderData[$order['order_id']]['type'] = 'order';
                $orderData[$order['order_id']]['id'] = $order['increment_id'];
                $orderData[$order['order_id']]['attributes']['customer'] = array('firstName'=>$order['firstname'],'lastName'=>$order['lastname'],'email'=>$order['email'],'phone'=>$order['telephone'],'address1'=>$order['street'],'address1'=>preg_replace("/\r|\n/", "", $order['street']),'city'=>$order['city'],'zip'=>$order['postcode'],'province'=>$order['region'],'country'=>$order['country_id']);
                $orderData[$order['order_id']]['attributes']['contractSales'] = array();
                $orderData[$order['order_id']]['attributes']['lineItems'][] = array('id'=>$order['item_id'], 
                    'productSku'=>$order['sku'],
                    'price'=>(float)$order['price'],
                    'quantity'=>(float)$order['qty'],
                    'serialNumber'=>''
                );
            }
        }

        return $orderData;
    }

    public function importOrders($output)
    {
        $getData = $this->_getResource()->getShipments($this->_allowedProduct);
        $formatedData = $this->prepareOrderData($getData);
        $count = -1;
        if (count($formatedData)) {
            $this->openFiles();
            $this->writeOrderCsv(array('Order Id','Status','Message'));
            foreach ($formatedData as $order_id => $shipment) {
                $checkData = $this->isCreateOrder($shipment, $order_id);
                if ($checkData === false) {
                    $requestData['data'] = $shipment;
                    $time = time();
                    $orderId = $shipment['id'];
                    //print_r($requestData); 
                    $finalarray = $this->getClydeOrderRequest($requestData, $orderId, $time, $output);
                    //print_r($finalarray);  exit;
                    if($finalarray !== false){
                        $this->_getResource()->getOrderDataInTable($shipment['id'], $order_id, $shipment);
                        if($output){
                            $output->writeln('<info>Order: '.$shipment['id'].' sync successfully</info>');
                            $this->writeOrderCsv(array($shipment['id'],'Success','Sync successfully'));
                        }

                        $count++;
                    }
                }
            }
        }else{
            $count = 0;
        }

        return $count;
    }

    public function getClydeOrderRequest($requestData, $orderId, $time, $output)
    {
        $data = $this->_apiClyde->setClydeOrder($requestData, $orderId, $time);
        if (isset($data['errors'])) {
            $value = $data['errors'];
            if (isset($value[0]['code'])) {
                $detail = isset($value[0]['detail'])?$value[0]['detail']:'';
                $title = isset($value[0]['title'])?$value[0]['title']:'';
                $this->writeOrderCsv(array($orderId,'Errors',$title.' - '.$detail));
                if ($output) {
                    $output->writeln('<comment>Order Id: '.$orderId.' '.$title.', '.$detail.'</comment>');
                }
            }
            
            return false;
        }

        $output->writeln('<comment>'.json_encode($data).'</comment>');
        return $data;
    }

    public function isCreateOrder($orderData, $order_id)
    {
        $shipment_id = $orderData['id'];
        $data = $this->_getResource()->getSyncOrder($order_id, $shipment_id);
         if (count($data) > 0) {
            $requestData = json_encode($orderData, JSON_UNESCAPED_SLASHES);
            $result = $data[0];
            $resultOfValue = json_decode($result['clyde_order_json'], true);
            $existsData = json_encode($resultOfValue, JSON_UNESCAPED_SLASHES);
            if (strlen($requestData) == strlen($existsData)) {
                return true;
            }
         }

        return false;
    }

    public function openFiles() 
    {
        $files = 'order_sync_on-'.date('y-m-d').'.csv';
        $this->addedOrder = $this->directory->openFile($this->createOrderFile($files), 'w+');
    }

    public function readFiles() 
    {
        $files = 'order_sync_on-'.date('y-m-d').'.csv';
        $dirPath = $this->setMediaDirectory();
        $filePath =  $dirPath.'/'. $files;
        $this->addedOrder = $this->directory->openFile($filePath, 'a');
    }

    public function createOrderFile($file) 
    {
        $dirPath = $this->setMediaDirectory();
        $filePath =  $dirPath.'/'. $file;
        if (!file_exists($filePath)) {
            file_put_contents($filePath, '');
        }

        return $filePath;

    }

    public function setMediaDirectory()
    {
        $fileDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $dirPath =  $fileDirectory . "synccsv/".date('Y-m-d');

        if (!file_exists($dirPath)) {
            $this->_files->mkdir($dirPath, 0777);
        }

        return $dirPath;
    }

    public function writeOrderCsv($value) 
    {
        $this->writeCsv($this->addedOrder, $value);
    }

    public function writeCsv($resource, $value) 
    {
        $resource->writeCsv($value);
    }

    public function addCronSyncToschedule()
    {
        return $this->_getResource()->addCronSyncToschedule();
    }

}