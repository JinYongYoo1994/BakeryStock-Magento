<?php
namespace Clyde\Warranty\Block\Adminhtml\Customer;

use Magento\Customer\Controller\RegistryConstants;

use Magento\Ui\Component\Layout\Tabs\TabInterface;
 
class Customerwarranty extends \Magento\Framework\View\Element\Template implements TabInterface
{
    protected $_coreRegistry;
 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
 
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
 
    public function getTabLabel()
    {
        return __('Warranty Info');
    }
    
    public function getTabTitle()
    {
        return __('Warranty Info');
    }
    
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }

        return false;
    }
 
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }

        return true;
    }
  
    public function getTabClass()
    {
        return '';
    }
   
    public function getTabUrl()
    {
   
        return $this->getUrl('warranty/customer/index', array('_current' => true));
    }
    
    public function isAjaxLoaded()
    {
        return true;
    }
}