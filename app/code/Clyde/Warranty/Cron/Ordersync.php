<?php

namespace Clyde\Warranty\Cron;

class Ordersync
{
    protected $_order;
    protected $logger;
    protected $_importlogger;
    public function __construct(
        \Clyde\Warranty\Model\Order $order,
        \Clyde\Warranty\Model\Logger $importlogger
    ) {
        $this->_order = $order;
        $this->_importlogger = $importlogger;
    }

    public function execute() 
    {
        
        try{
            $this->_importlogger->debugLog((string)__('Cron start plan sync'));
            $this->_order->importOrder(null);
            $this->_importlogger->debugLog((string)__('Cron end plan sync'));
        }
        catch(\Exception $e){
            $this->_importlogger->debugLog($e->getMessage());
            $this->_importlogger->errorLog($e->getMessage());
        }
    }
}