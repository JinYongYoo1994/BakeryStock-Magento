<?php

namespace Clyde\Warranty\Block\Adminhtml;

class Warrantysaleinfo extends \Magento\Framework\View\Element\Template
{

    protected $_template = 'warrantysale/warrantysaleinfo.phtml';

    protected $_collectionFactory;

    protected $_shipment;

    protected $_helper;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Clyde\Warranty\Model\WarrantysaleFactory $collectionFactory,
        \Magento\Sales\Model\Order\Shipment $shipment,
        \Clyde\Warranty\Helper\Data $helper,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_shipment = $shipment;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getShipmentID()
    {

        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $shipmentCollection = $this->_shipment->load($shipmentId);
        $incrementId = $shipmentCollection->getIncrementId();
        $collection = $this->_collectionFactory->create();
        $data = $collection->getCollection()->addFieldToFilter('shipment_id', $incrementId);
        if(count($data)>0){
            foreach($data as $row){
            $status =  $row['status'];
            $contract_id =  $row['contract_sale_id'];
            }

        if($status == 'Success')
        {
            $response = "<h3 style='display:inline-block;color:#007bdb'>Clyde Contract Id </h3> ".$contract_id;
        } else {
            $response = "<h3 style='display:inline-block;color:#007bdb'>Clyde Response </h3> ".$this->_helper->getClydeErrorMessage()."<a href='".$this->getUrl('warranty/index/clyde').'id/'.$incrementId.'/return_url/1'."'> Try Again</a>";
        }

        return $response;
        }
    }


}
