<?php

namespace Codecryption\Certificate\Helper;

use Magento\Framework\App\ObjectManager;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getEnableModule()
    {
        $value = $this->scopeConfig->getValue('certificate/general/enable',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $value;
    }
    
    public function getAttributValue()
    {
        $value = $this->scopeConfig->getValue('certificate/general/product_attribute',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $value;
    }

    public function getMapedField()
    {
        $value = $this->scopeConfig->getValue('certificate/general/certificate_field_map',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (empty($value)) return false;

        if ($this->isSerialized($value)) {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Unserialize\Unserialize::class);
        } else {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
        }

        $products = $unserializer->unserialize($value);
        return $this->getProductSkuArray($products);
    }

    public function getProductSkuArray($products)
    {
    	$values = array();
        if(count($products)>0){
        	foreach($products as $sku){
        		$values[$sku['attribute_field']] = $sku['cms_block'];
        	}
        }
        return $values;
    }


    private function isSerialized($value)
    {
        return (boolean) preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }
}