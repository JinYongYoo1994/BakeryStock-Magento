<?php
namespace Clyde\Warranty\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

class Attribute implements \Magento\Framework\Option\ArrayInterface
{
     public function __construct(
         CollectionFactory $collectionFactory
     ) {
           $this->_collectionFactory = $collectionFactory;
     }

    public function toOptionArray()
    {
        return $this->getAttributes();
    }
    public function getAttributes() 
    {
        $collection = $this->_collectionFactory->create();
        foreach ($collection as $items) {
           if(!empty($items['frontend_label'])){
             $attr_groups[] = array('value'=>$items['attribute_code'],
                                     'label'=>$items['frontend_label']);
           }
        }

        return $attr_groups;
    }
}