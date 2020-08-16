<?php
namespace Clyde\Warranty\Model\Api;

class Clyde extends \Magento\Framework\Model\AbstractModel
{
    CONST CONTRACTS_URL = 'contracts';
    CONST ORDER_URL = 'orders';
    CONST CONTRACTS_SALES_URL = 'contract-sales';
    CONST CONTRACTS_PRODUCT_URL = 'products';
    CONST CALL_CONSTANT_ONE = '01';
    CONST CALL_CONSTANT_SECOND = '02';
    CONST CALL_CONSTANT_THIRD = '03';
    CONST CALL_CONSTANT_FOUR = '04';
    CONST CALL_CONSTANT_FIVE = '05';

    protected $_helper;

    protected $_importlogger;
    protected $_orderManager;
    protected $messageManager;
    protected $_warrantysale;
    protected $_httpstatuscode;
    protected $encryptor;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Clyde\Warranty\Helper\Data $helper,
        \Clyde\Warranty\Model\Logger $logger,
        \Magento\Sales\Model\Order $orderManager,
        \Clyde\Warranty\Model\WarrantysaleFactory $warrantysale,
        \Clyde\Warranty\Model\Api\Httpstatuscode $httpstatuscode,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        array $data = array()
    ) {
        $this->_importlogger = $logger;
        $this->_helper = $helper;
        $this->_orderManager = $orderManager;
        $this->_objectManager = $objectmanager;
        $this->_warrantysale = $warrantysale;
        $this->_httpstatuscode = $httpstatuscode;
        $this->encryptor = $encryptor;
        parent::__construct($context, $registry);
    }

    public function getHttpErrorCode($errorCode)
    {
        return $this->_httpstatuscode->getHttpDtatusCode($errorCode);
    }

    public function getToNum($data)
    {
        $alphabet = array( 'a', 'b', 'c', 'd', 'e',
                         'f', 'g', 'h', 'i', 'j',
                         'k', 'l', 'm', 'n', 'o',
                         'p', 'q', 'r', 's', 't',
                         'u', 'v', 'w', 'x', 'y',
                         'z','1','2','3','4','5','6','7','8','9','0',' ',
                         'A', 'B', 'C', 'D', 'E',
                         'F', 'G', 'H', 'I', 'J',
                         'K', 'L', 'M', 'N', 'O',
                         'P', 'Q', 'R', 'S', 'T',
                         'U', 'V', 'W', 'X', 'Y',
                         'Z','-'
                         );
          $alpha_flip = array_flip($alphabet);
          $return_value = '';
          $length = strlen($data);
          for ($i = 0; $i < $length; $i++) {
            if(isset($alpha_flip[$data[$i]])){
               $return_value = (string)$return_value.(string)($alpha_flip[$data[$i]]);
            }
          }

          return (string)$return_value;
    }

    public function _postApiRequest($requestArr , $gatewayUrl , $method = 'POST',$key,$orderId,$time)
    {
        $requestString = $requestArr;
        $apikey = $this->getClydeAPIkey();
        $header = array('Authorization:' .$apikey.':'.$key,
            'X-Auth-Timestamp: '.$time,
            'X-Auth-Nonce: '.$orderId,
            'Content-Type: application/vnd.api+json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gatewayUrl);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);

        $data = curl_exec($ch);
        
        $responce = curl_getinfo($ch);
        if (!curl_errno($ch) && empty($data) !== false) {
           $errors['errors'][] = array('status'=>$responce['http_code'],'code' => $responce['http_code']*100,'title'=>$this->getHttpErrorCode($responce['http_code']));
           return json_encode($errors);
        }elseif(!($data)){
            $this->_importlogger->errorLog($responce['http_code']);
            $errors['errors'][] = array('status'=>$responce['http_code'],'code' => $responce['http_code']*100,'title'=>$this->getHttpErrorCode($responce['http_code']));
            return json_encode($errors);
        }

        curl_close($ch);
        unset($ch);
        return $data;
    }

    public function _getApiRequest($requestArr , $gatewayUrl , $method = 'GET',$key,$orderId,$time)
    {
        $requestString = $requestArr;
        $header = array('Authorization: '.$this->getClydeAPIkey().':'.$key,
                        'X-Auth-Timestamp: '.$time,
                        'X-Auth-Nonce: '.$orderId,
                        'accept: application/vnd.api+json'
                        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gatewayUrl);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        $data = curl_exec($ch);
        
        $responce = curl_getinfo($ch);
        if (!curl_errno($ch) && empty($data) !== false) {
           $errors['errors'][] = array('status'=>$responce['http_code'],'code' => $responce['http_code']*100,'title'=>$this->getHttpErrorCode($responce['http_code']));
           return json_encode($errors);
        }elseif(!($data)){
            $this->_importlogger->errorLog($responce['http_code']);
            $errors['errors'][] = array('status'=>$responce['http_code'],'code' => $responce['http_code']*100,'title'=>$this->getHttpErrorCode($responce['http_code']));
            return json_encode($errors);
        }

        curl_close($ch);
        unset($ch);
        return $data;
    }

    public function makeMessage($verb, $url, $body, $nonce, $timestamp)
    {

      $message = json_encode(array($verb, $url, $body, "".$nonce, "".$timestamp), JSON_UNESCAPED_SLASHES);
      return $message;

    }


    public function signPhpMessage($secret, $verb, $url, $body, $nonce, $timestamp)
    {
        $message = $this->makeMessage($verb, $url, $body, $nonce, $timestamp);
        $hash_digest = hash('sha256', $message, false);
        $hash_digest = hex2bin($hash_digest);
        $hmac_digest = hash_hmac('sha512', $url.$hash_digest, utf8_encode($secret), false);
        $hmac_digest = hex2bin($hmac_digest);
        $hmac_digest = base64_encode($hmac_digest);
        return $hmac_digest;
    }

    public function makeMessagePost($verb, $url, $body, $nonce, $timestamp) 
    {

        $message = json_encode(array($verb, $url, $body, $nonce, ''.$timestamp), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return $message;

    }

    public function signPhpMessagePost($secret, $verb, $url, $message, $nonce, $timestamp) 
    {
        $hash_digest = hash('sha256', $message, true);
        $hmac_digest = hash_hmac('sha512', $url.$hash_digest, utf8_encode($secret), true);
        $hmac_digest = base64_encode($hmac_digest);
        return $hmac_digest;

    }

    public function getClydeSecretkey()
    {
        return $this->_helper->getClydeSecretkey();
    }

    public function getClydeAPIkey()
    {
        return $this->_helper->getClydeAPIkey();
    }

    public function getClydeurl()
    {
        return $this->_helper->getClydeurl();
    }

    public function getClydeContracts()
    {
        $url = $this->getClydeurl().self::CONTRACTS_URL;
        $time = $orderId = (string)time();
        $key = $this->signPhpMessage($this->getClydeSecretkey(), 'GET', $url, '', $orderId, $time);
        $result = $this->_getApiRequest('', $url, 'GET', $key, $orderId, $time);
        return $this->processResponce($result);
    }

    public function getClydeProduct($page)
    {
        $url = $this->getClydeurl().self::CONTRACTS_PRODUCT_URL.'?page='.$page;
        $time = (string)time();
        $orderId = (string)time().self::CALL_CONSTANT_ONE;
        $key = $this->signPhpMessage($this->getClydeSecretkey(), 'GET', $url, '', $orderId, $time);
        $result = $this->_getApiRequest('', $url, 'GET', $key, $orderId, $time);
        return $this->processResponce($result);
    }

    public function getProductBySku($sku)
    {
        $number = $this->encryptor->hash($sku);
        $url = $this->getClydeurl().self::CONTRACTS_PRODUCT_URL.'/'.rawurlencode($sku);
        $time = (string)time();
        $orderId = (string)time().self::CALL_CONSTANT_SECOND.$number;
        $key = $this->signPhpMessage($this->getClydeSecretkey(), 'GET', $url, '', $orderId, $time);
        $result = $this->_getApiRequest('', $url, 'GET', $key, $orderId, $time);
        return $this->processResponce($result);
    }

    public function getProductCreate($requestData,$id)
    {
        $url = $this->getClydeurl().self::CONTRACTS_PRODUCT_URL;
        $time = time();
        $orderId = (string)time().self::CALL_CONSTANT_THIRD.$id;
        $requestJson = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        $message = $this->makeMessagePost('POST', $url, $requestData, $orderId.'', $time.'');
        $key = $this->signPhpMessagePost($this->getClydeSecretkey(), 'POST', $url, $message, $orderId.'', $time.'');
        $result = $this->_postApiRequest($requestJson, $url, 'POST', $key, $orderId.'', $time.'');
        return $this->processResponce($result);
    }

    public function getProductUpdate($sku, $requestData, $id)
    {
        $url = $this->getClydeurl().self::CONTRACTS_PRODUCT_URL.'/'.rawurlencode($sku);
        $time = (string)time();
        $number = $this->getToNum($sku);
        $orderId = (string)time().$this->encryptor->hash(self::CALL_CONSTANT_FOUR.$id.$sku);
        $requestJson = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        $message = $this->makeMessagePost('PUT', $url, $requestData, $orderId.'', $time.'');
        $key = $this->signPhpMessagePost($this->getClydeSecretkey(), 'PUT', $url, $message, $orderId.'', $time.'');
        $result = $this->_postApiRequest($requestJson, $url, 'PUT', $key, $orderId.'', $time.'');
        return $this->processResponce($result);
    }

    public function setClydeOrder($requestData , $orderId, $time)
    {
        $url = $this->getClydeurl().self::ORDER_URL;
        $requestJson = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        $message = $this->makeMessagePost('POST', $url, $requestData, $orderId.'', $time.'');
        $key = $this->signPhpMessagePost($this->getClydeSecretkey(), 'POST', $url, $message, $orderId.'', $time.'');
        $result = $this->_postApiRequest($requestJson, $url, 'POST', $key, $orderId.'', $time.'');
        return $this->processResponce($result);
    }

    public function setMessageManagerClyde($messageManager) 
    {
        $this->messageManager = $messageManager;
    }

    public function setContractSales($contractid, $error = false)
    {
        $url = $this->getClydeurl().self::CONTRACTS_SALES_URL.'/'.$contractid;
        $time = $orderId = (string)time();
        $key = $this->signPhpMessage($this->getClydeSecretkey(), 'DELETE', $url, '', $orderId, $time);
        $result = $this->_getApiRequest('', $url, 'DELETE', $key, $orderId, $time);
        return $this->processResponce($result, $error);
    }

    public function processResponce($value, $error = false)
    {
        $errorCodeValidate = array(/*'40100',*/'0','40102','42204');
        $value = json_decode($value, true);
        if(isset($value['errors'])){
            $data = $value['errors'];
            if(isset($data[0]['code']) && $data[0]['code'] != '20400' && (in_array($data[0]['code'], $errorCodeValidate) || $error === true)){
                $this->_importlogger->errorLog($data);
                $detail = isset($data[0]['detail'])?$data[0]['detail']:'';
                if(empty($this->messageManager) !== true){
                    $this->messageManager->addError(__("Clyde Error Message: ".$data[0]['title'].', '.$detail));
                }

               // throw new \Exception(__($data[0]['title'].', '.$detail));
            }

            $this->_importlogger->errorLog($value);
            return $value;
        }

        return $value;
    }

    public function checkIsItemeProcessed($orderId)
    {
        $warrantysaledatafal = $this->_warrantysale->create();
        $collection = $warrantysaledatafal->getCollection()->addFieldToFilter('order_id', $orderId);
        $itemIds = array();
        if($collection->count() > 0){
            foreach($collection as $sales){
                $data = json_encode($sales->getProcessedItemids());
                $itemIds = array_merge($itemIds, $data);
            }
        }

        return $itemIds;
    }

    public function setClydedata($order)
    {
        $orderData = $order;
        $warrantyid = $this->_helper->getWarrantyCount($orderData);
        $requestData = array();
        $flag=1;
        $orderId = $order->getIncrementId();
        $data['type'] = "order";
        $data['id'] = $orderId;
        $shippingAddressObj = $orderData->getShippingAddress();
        $shippingAddressArray = $shippingAddressObj->getData();
        $processedItem = array();
        $alreadyProccessed = $this->checkIsItemeProcessed($order->getId());
        $temparray = array();
        $finalarray = array();
        $lineItems = array();

        if(count($warrantyid)){
            foreach ($order->getItemsCollection() as $item) {
                $items = $item->getOrderItem();  
            $newvar = json_decode($items['warranty_info'], true);
                if(empty($newvar) !== true && in_array($items['product_id'], $warrantyid) && !in_array($items['item_id'], $alreadyProccessed)){
                    $sku = str_replace(' - '.$newvar['sku'],'',$items->getSku());
                    $temparray[] = array(
                            "lineItemId"=>$items['item_id'],
                            "productSku"=>$sku,
                            "contractSku"=>$newvar['sku'],
                            "productPrice"=>(float)$newvar['product_price'],
                            "contractPrice"=>(float)$newvar['customer_cost'],
                            "serialNumber"=>null);

                    $lineItems[] = array(
                            "id"=>$items->getProductId(),
                            "productSku"=>$sku,
                            "price"=>(float)$newvar['product_price'],
                            "quantity"=>'',//(float)$item->getQty(),
                            "serialNumber"=>null);
                       $processedItem[] = $items['item_id'];
                }
            }

            if(count($temparray) > 0){
                $data['attributes'] = array(
                "customer"=>array("firstName"=>$shippingAddressArray['firstname'],
                                "lastName" => $shippingAddressArray['lastname'],
                                "email"=>$shippingAddressArray['email'],
                                "phone"=>$shippingAddressArray['telephone'],
                                "address1"=>preg_replace("/\r|\n/", "", $shippingAddressArray['street']),
                                "address2"=> "",
                                "city"=>$shippingAddressArray['city'],
                                "province"=>$shippingAddressArray['region'],
                                "zip"=>$shippingAddressArray['postcode'],
                                "country"=>$shippingAddressArray['country_id'] ),
                'contractSales'  => $temparray,
                'lineItems'=>$lineItems);
                $requestData['data'] = $data;
                $time = time();
                $nonce = $orderId;
                //echo "<prE>";
                //print_r($shippingAddressArray);
                //print_r($requestData); exit;
                $finalarray = $this->setClydeOrder($requestData, $orderId, $time);
                $this->_importlogger->errorLog($finalarray);
            }
            
            $finalarray['processed'] = $processedItem;
            return $finalarray;
        }
    }
}
