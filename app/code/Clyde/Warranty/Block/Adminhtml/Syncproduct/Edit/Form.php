<?php
namespace Clyde\Warranty\Block\Adminhtml\Syncproduct\Edit;
 
use \Magento\Backend\Block\Widget\Form\Generic;
 
class Form extends Generic
{
 
    protected $_systemStore;

    protected $_helper;
 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Clyde\Warranty\Helper\Data $helper,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->_helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
    public function getHelper()
    {
        return $this->_helper;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('import_form');
        $this->setTitle(__('Warranty Plan Import'));
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('Clyde_Warranty::warranty/productsync.phtml');
        return $this;
    }
}