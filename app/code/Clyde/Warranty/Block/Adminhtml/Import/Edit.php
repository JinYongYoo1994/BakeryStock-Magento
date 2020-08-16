<?php
namespace Clyde\Warranty\Block\Adminhtml\Import;
 
use Magento\Backend\Block\Widget\Form\Container;
 
class Edit extends Container
{

    protected $_coreRegistry = null;
 
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
 
    protected function _construct()
    {
        $this->_objectId = 'import_id';
        $this->_blockGroup = 'Clyde_Warranty';
        $this->_controller = 'adminhtml_import';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Import Plan using xlsx'));
        $this->buttonList->remove('reset');
        $this->buttonList->remove('back');
        $this->buttonList->remove('save');    
 
    }
 
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
 
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('warranty/plan/import', array('_current' => true, 'back' => 'edit', 'active_tab' => ''));
    }
}