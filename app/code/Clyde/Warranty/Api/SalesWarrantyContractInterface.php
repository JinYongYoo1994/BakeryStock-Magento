<?php 
namespace Clyde\Warranty\Api;
 
 
interface SalesWarrantyContractInterface
{


    /**
     * GET for SalesData api
     * @param string $id
     * @return associativeArray()
     */
    
    public function getClydeContract($id);

    /**
     * GET for SalesData api
     * @return associativeArray()
     */
    
    public function getContractRetry();
}
