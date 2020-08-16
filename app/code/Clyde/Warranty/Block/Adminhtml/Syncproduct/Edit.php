<?php
namespace Clyde\Warranty\Block\Adminhtml\Syncproduct;
 
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
        $this->_objectId = 'sync_id';
        $this->_blockGroup = 'Clyde_Warranty';
        $this->_controller = 'adminhtml_syncproduct';
        parent::_construct();
        //$this->buttonList->update('save', 'label', __('Sync Product'));
        $this->buttonList->remove('reset');
        $this->buttonList->remove('back');
        $this->buttonList->remove('save');

            
 
    }
 
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
 
}