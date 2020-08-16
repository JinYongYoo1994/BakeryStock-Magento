<?php
namespace Clyde\Warranty\Model;
use Magento\Framework\App\Filesystem\DirectoryList;
class Plan extends \Magento\Framework\Model\AbstractModel
{
    
    CONST ADD_SKU = 'add_sku.csv';
    CONST ADD_SKU_PLAN = 'plan_import_sku.csv';
    protected $_apiClyde;
    protected $_filesystem;
    protected $addedSku;
    protected $importedFiles;
    protected $_planconnection;
    protected $_warranty;
    protected $_resourceModel;
    protected $connection;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Clyde\Warranty\Model\Api\Clyde $apiClyde,
        \Magento\Framework\Filesystem $filesystem,
        \Clyde\Warranty\Model\Planconnection $planconnection,
        \Clyde\Warranty\Model\WarrantyFactory $warranty,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = array()
    ) {
        $this->_apiClyde = $apiClyde;
        $this->_filesystem = $filesystem;
        $this->_planconnection = $planconnection;
        $this->_warranty = $warranty;
        $this->_resourceModel = $resource;
        parent::__construct($context, $registry);
    }
    
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resourceModel->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        }

        return $this->connection;
    }

    public function sortByterms($a, $b)
    {
        $a = $a['attributes']['term'];
        $b = $b['attributes']['term'];

        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
    }

    public function getWarrantyPlanByRuleId($productSku = null)
    {
        $finalArray = array();
        if($productSku != ''){
           $result = $this->_apiClyde->getProductBySku($productSku);
           if(!isset($result['errors'])){
            if(isset($result['data']['attributes']['contracts']) && empty($result['data']['attributes']['contracts']) !== true){
                  $contracts = $result['data']['attributes']['contracts'];
                  usort($contracts, array('\Clyde\Warranty\Model\Plan','sortByterms'));                
                  $i = 0;
                  foreach($contracts as $contract){
                      $contactdata = $contract['attributes'];
                      $contactdata['warranty_id'] = 1;
                      $contactdata['name'] = $contactdata['category'];
                      $contactdata['status'] = 1;
                      $contactdata['year_term'] = $contract['attributes']['term'];
                      $contactdata['plan_id'] = $contract['attributes']['sku'];
                      $contactdata['customer_cost'] = $contract['attributes']['recommendedPrice'];
                      $finalArray[] = $contactdata;
                      $i++;
                  } 
            }
           }
        }

        return $finalArray;
    }

    public function getAppliedRuleIds($ruleIds)
    {
        $adapter = $this->getConnection();
        $table = $adapter->getTableName('clyde_warranty'); 
        $select = $adapter->select()
            ->from($table, '*')
            ->where('warranty_id IN (?)', $ruleIds)
            ->where('status = ?', \Clyde\Warranty\Model\Status::ENABLED);

        $result = $adapter->fetchAll($select);
        return $result;
    }

    public function getQuoteItemById($itemId)
    {
        
        $adapter = $this->getConnection();
        $table = $adapter->getTableName('quote_item');
        $select = $adapter->select()
            ->from($table, '*')
            ->where('item_id = ?', $itemId);

        $result = $adapter->fetchRow($select);
        return $result;
    }
    
    public function updateWarrantyInfo($itemId, $value)
    {
        $adapter = $this->getConnection();
        $where = array($adapter->quoteInto('item_id = ?', $itemId));
        return $adapter->update($adapter->getTableName('quote_item'), $value, $where);
    }
}