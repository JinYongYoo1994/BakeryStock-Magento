<?php

namespace Clyde\Warranty\Cron;

class Productsync
{
    protected $_importproduct;
    protected $logger;
    protected $_importlogger;
    protected $_helper;
    public function __construct(
        \Clyde\Warranty\Model\ProductSyncToClyde $importproduct,
        \Clyde\Warranty\Model\Logger $importlogger,
        \Clyde\Warranty\Helper\Data $helper
    ) {
        $this->_importproduct = $importproduct;
        $this->_importlogger = $importlogger;
        $this->_helper = $helper;
    }

    public function execute() 
    {
        
        try{
            $this->_importlogger->debugLog((string)__('Cron start'));
            $this->_importproduct->getSyncProductCommandAndCron(null, $this->_helper->getProductLimit(), 1);
            $this->_importlogger->debugLog((string)__('Cron end'));
        }
        catch(\Exception $e){
            $this->_importlogger->errorLog($e->getMessage());
            $this->_importlogger->debugLog($e->getMessage());
        }
    }
}