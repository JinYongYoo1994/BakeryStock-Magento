<?php

namespace Pimgento\Api\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;

/**
 * Class View
 *
 * @category  Class
 * @package   Pimgento\Api\Controller\Adminhtml\Log
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class View extends Action
{
    /**
     * Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();

        /* @var $block \Pimgento\Api\Block\Adminhtml\Log\View */
        $block = $this->_view->getLayout()->getBlock('adminhtml.pimgento.log.view');
        $block->setLogId(
            $this->getRequest()->getParam('log_id')
        );

        $this->_setActiveMenu('Magento_Backend::system');
        $this->_addBreadcrumb(__('Pimgento'), __('Log'));

        $this->_view->renderLayout();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Pimgento_Api::pimgento_log');
    }
}
