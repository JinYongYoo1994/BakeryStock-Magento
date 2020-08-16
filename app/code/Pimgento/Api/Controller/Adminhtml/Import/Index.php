<?php

namespace Pimgento\Api\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;

/**
 * Class Index
 *
 * @category  Class
 * @package   Pimgento\Api\Controller\Adminhtml\Import
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class Index extends Action
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_view->loadLayout();

        $this->_setActiveMenu('Magento_Backend::system');
        $this->_addBreadcrumb(__('Pimgento'), __('Import API'));

        $this->_view->renderLayout();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Pimgento_Api::import');
    }
}
