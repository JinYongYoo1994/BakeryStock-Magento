<?php
namespace Clyde\Warranty\Block\Customer;

class Warrantyinfo extends \Magento\Framework\View\Element\Template
{
    public $_customerSession;
    
    public $_filterManager;
    
    public $_warrantyFactory;
    
    public $warrantytype;
    
    protected $_currency;
    
    public $_helper;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Clyde\Warranty\Model\Customerwarranty $warrantyFactory,
        \Clyde\Warranty\Model\Warranty\Warrantytype $warrantytype,
        \Magento\Framework\Pricing\Helper\Data $currency,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_warrantyFactory = $warrantyFactory;
        $this->_helper = $helper;   
        $this->warrantytype = $warrantytype;
        $this->_currency = $currency;
        parent::__construct($context, $data);
    }
    
    protected  function _construct()
    {
        parent::_construct();
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }

        $collection = $this->_warrantyFactory->getCollection();
        $collection->join(
            array('sales_item' => $collection->getTable('sales_order_item')),
            'main_table.item_id = sales_item.item_id',
            array('name','sku','warranty_info')
        )->join(
            array('sales_order' => $collection->getTable('sales_order')),
            'sales_item.order_id = sales_order.entity_id',
            array('increment_id','created_at')
        );
        $collection->addFieldToFilter('main_table.customer_id', $customerId);
        $collection->setOrder('main_table.customerwarranty_id', 'DESC');
        
        $this->setCollection($collection);
    }
 
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'simplenews.news.list.pager'
        );

        $pageerLimit = $pager->getAvailableLimit();
        $pageerLimit[$this->_helper->getWarrantuPagination()]=$this->_helper->getWarrantuPagination();
        asort($pageerLimit);
        $params = $this->getRequest()->getParam('limit');
        $page_limit = ($params)?$params:$this->_helper->getWarrantuPagination();
        $pager->setLimit($page_limit)->setAvailableLimit($pageerLimit)
            ->setShowAmounts(false)
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
 
        return $this;
    }
 
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getPrice($price)
    {
        return $this->_currency->currency($price, true, false);
    }

    public function calulateWarrantyPeriod($warrantyCustomer , $data , $formate = 'html')
    {
        $purchased = explode(" ", $warrantyCustomer->getCreatedAt());
         if(isset($data['year_term']) && $data['year_term'] > 0) {
                $start = date('Y-m-d');
                $dateString = $warrantyCustomer->getCreatedAt();
                $t = strtotime($dateString);
                $t2 = strtotime('+'.$data['year_term'].' year', $t);
                $end = date('Y-m-d', $t2);
                $diff = strtotime($end) - strtotime($start);
                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                $dateString = '';//= sprintf("%d years, %d months, %d days", $years, $months, $days);
                if($years > 0){
                    $dateString .= $years.' years ';
                }

                if($months > 0){
                    $dateString .= $months.' months ';
                }

                if($days > 0){
                    $dateString .= $days.' days ';
                }

                $remaining_days = ($diff > 0) ? 1 : 0;

                if($years == 0 && $months == 0 && $days == 0){
                    $class = 'remaining';
                    if($formate == 'value'){
                        return array('remaining'=>__('Today'));
                    }
                }elseif($remaining_days == 1){
                    $class = 'remaining';
                    if($formate == 'value'){
                        return array('remaining'=>$dateString);
                    }
                }
                else
                {
                    $class = 'expired';
                    if($formate == 'value'){
                        return array('expired'=>$dateString);
                    }
                } 
         } else {
              $purchased = 'Expired';
              $class = 'remaining';
         }

         return '<p><b>'.__('Purchased Date').' : </b><span class="'.$class.'">'.__($purchased[0]).'</span></p>';
    }
}
