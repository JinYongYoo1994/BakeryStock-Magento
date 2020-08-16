<?php
namespace Clyde\Warranty\Model\Observer;

class OrderAfterEvent implements \Magento\Framework\Event\ObserverInterface
{
    protected $warranty;
    
    protected $customerwarranty;
    
    protected $_helper;
    
    protected $orderFacory;
    
    public function __construct(
        \Clyde\Warranty\Helper\Data $helper,
        \Clyde\Warranty\Model\Plan $warrantyFactory,
        \Clyde\Warranty\Model\Customerwarranty $customerwarrantyFactory,
        \Magento\Sales\Model\Order $orderFacory
    ) {
        $this->_helper = $helper;
        $this->warranty = $warrantyFactory;
        $this->customerwarranty = $customerwarrantyFactory;
        $this->orderFacory = $orderFacory;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($this->_helper->getEnableModule() == 1 && $order->getId()) {
            foreach($order->getAllItems() as $orderItem) {
                $this->addToCustomerWarranty($orderItem, $order);
            }
        }
    }
   
    private function checkCustomerWarrantyExists($orderItem)
    {
        $model = $this->customerwarranty->getCollection();
        $model->addFieldToFilter('item_id', $orderItem->getId());
        $data = $model->getFirstItem();
        if ($model->count()>0) {
            return $data->getId();
        }

        return false;
    }
    
    private function addToCustomerWarranty($orderItem,$order)
    {

        if($orderItem->getWarrantyInfo() != ''){
        $warrantyBy = $this->_helper->decryptString($orderItem->getWarrantyInfo());
        
        $productData = $orderItem->getProduct()->getData();
        $id = $this->checkCustomerWarrantyExists($orderItem);
        if (empty($warrantyBy) !== true && $id === false) {
            $model = $this->customerwarranty;
            $data = array();
            $data['customer_id'] = $order->getCustomerId();
            $data['order_id'] = $order->getId();
            $data['item_id'] = $orderItem->getId();
            $data['plan_id'] = $warrantyBy['plan_id'];
            $data['customer_cost'] = $this->_helper->calculatePrice($warrantyBy, $orderItem->getProduct());
            $data['product_id'] = $orderItem->getProductId();
            $data['created_time'] = date('Y-m-d H:i:s');
            $model->setData($data);
            $model->save();
            $model->unsData();
        }
        }
    }
}