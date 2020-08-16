<?php
namespace Clyde\Warranty\Controller\Adminhtml\Rule;

class Grid extends \Clyde\Warranty\Controller\Adminhtml\Products\Product
{
   
    protected $resultRawFactory;

    protected $layoutFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    public function execute()
    {
        $item = $this->_initItem(true);
        if (!$item) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('warranty/plan/new', array('_current' => true, 'id' => null));
        }

        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'Clyde\Warranty\Block\Adminhtml\Rule\Plan\Edit\Tab\Plan',
                'category.plan.grid'
            )->toHtml()
        );
    }

    protected function _initItem($getRootInstead = false)
    {
        $id = (int)$this->getRequest()->getParam('id', false);
        $myModel = $this->_objectManager->create('Clyde\Warranty\Model\Plan');
        if ($id) {
            $myModel->load($id);            
        }

        $this->_objectManager->get('Magento\Framework\Registry')->register('warranty_content', $myModel);
        $this->_objectManager->get('Magento\Cms\Model\Wysiwyg\Config');
        return $myModel;
    }   
}
