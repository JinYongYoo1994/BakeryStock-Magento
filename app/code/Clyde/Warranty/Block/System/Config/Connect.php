<?php

namespace Clyde\Warranty\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Connect extends Field
{
     /**
     * @var string
     */
    protected $_api;
    protected $_helper;
    protected $_order;

    protected $_clydeproduct;
    public function __construct(
        \Clyde\Warranty\Model\Api\Clyde $api,
        \Clyde\Warranty\Helper\Data $helper,
        \Clyde\Warranty\Model\Clydeproduct $clydeproduct,
        \Clyde\Warranty\Model\Order $order,
        Context $context,
        array $data = array()
    ) {
        $this->_api = $api;
        $this->_helper = $helper;
        $this->_clydeproduct = $clydeproduct;
        $this->_order = $order;
        parent::__construct($context, $data);
    }

    protected $_template = 'Clyde_Warranty::system/config/connect.phtml';

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for collect button
     *
     * @return string
     */
    /*public function getAjaxUrl()
    {
        return $this->getUrl('warranty/system_config/connect');
    }*/

    public function getButtonText()
    {
        return __('Test Connection');
    }

    public function getButtonErrorText()
    {
        return __('Failed');
    }

    public function getButtonSuccessText()
    {
        return __('Connected');
    }

    /**
     * Generate collect button html
     *
     * @return string
     */
    public function setConnectValue($result)
    {
        $value = isset($result['success'])?1:0;
        $this->_helper->setClydeConnect($value);
    }

    public function isCronInitiate()
    {
       $value = $this->_helper->isClydeConnect();
       if($value == 1){
         $flage = $this->_helper->setClydeConnectData();
         if($flage == true){
            $this->_clydeproduct->addCronSyncToschedule();
            $this->_order->addCronSyncToschedule();
         }
       }
    }

    public function getConnect()
    {
       try {
            $connection = $this->_api->getClydeContracts();
            $result = array();
            if(isset($connection['errors'])){
                $data = $connection['errors'];
                $detail = isset($data[0]['detail'])?$data[0]['detail']:'';
                $result = array('error'=>$data[0]['title'].', '.$detail);
            }else{
                $result = array('success'=>1);
            }
       } catch (\Exception $e) {               
                $result = array('error'=>$e->getMessage());
       }

        $this->setConnectValue($result);
        return $result;
    }

    public function getButtonHtml()
    {
        $status = $this->getConnect();
        if($status['success'] == 1){
            $button = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                array(
                    'id' => 'connect_button',
                    'label' => $this->getButtonSuccessText(),
                )
            );
        }
        else
        {
            $button = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                array(
                    'id' => 'connect_button',
                    'label' => $this->getButtonErrorText(),
                )
            );
        }

        return $button->toHtml();
    }

}
