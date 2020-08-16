<?php 
namespace Clyde\Warranty\Api;
 
 
interface SalesWarrantyInterface
{


    /**
     * GET for SalesData api
     * @param string $status
     * @param string $fromDate
     * @param string $toDate
     * @return array()|associativeArray()
     */
    
    public function getSalesData($status, $fromDate = null, $toDate = null);
}
