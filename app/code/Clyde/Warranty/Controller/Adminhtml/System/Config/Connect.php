<?php

namespace Clyde\Warranty\Controller\Adminhtml\System\Config;

use \Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Controller\ResultFactory;

class Connect extends \Magento\Backend\App\Action
{
    
    protected $_logger;
    protected $_api;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Clyde\Warranty\Helper\Data $helper,
        \Clyde\Warranty\Model\Api\Clyde $api
    ) {
        $this->_api = $api;
        parent::__construct($context);
        $this->_helper = $helper;
    }

    public function execute()
    {
        try {
            $connection = $this->_api->getClydeContracts();
            $result = array();
            if (isset($connection['errors'])) {
                $data = $connection['errors'];
                $detail = isset($data[0]['detail'])?$data[0]['detail']:'';
                $result = array('error'=>$data[0]['title'].', '.$detail);
            } else {
                $result = array('success'=>1);
            }
        } catch (\Exception $e) {               
                $result = array('error'=>$e->getMessage());
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }
}