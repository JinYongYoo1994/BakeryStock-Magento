<?php
namespace Clyde\Warranty\Block\Adminhtml\Form\Field;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

class Attributes extends \Magento\Framework\View\Element\Html\Select
{
    
    protected $_collectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_collectionFactory = $collectionFactory;
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
    
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $collection = $this->_collectionFactory->create();
            foreach ($collection as $groupId => $groupLabel) {
               // print_r($groupId);
               // print_r($groupLabel); exit;
                $this->addOption($groupLabel['attribute_code'], addslashes($groupLabel['frontend_label']));
            }
        }

        return parent::_toHtml();
    }
}
