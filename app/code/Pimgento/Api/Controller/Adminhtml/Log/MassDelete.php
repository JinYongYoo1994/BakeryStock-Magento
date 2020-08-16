<?php

namespace Pimgento\Api\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Pimgento\Api\Api\LogRepositoryInterface;
use Pimgento\Api\Model\ResourceModel\Log\CollectionFactory;
use Pimgento\Api\Model\ResourceModel\Log\Collection;

/**
 * Class MassDelete
 *
 * @category  Class
 * @package   Pimgento\Api\Controller\Adminhtml\Log
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class MassDelete extends Action
{
    /**
     * This variable contains a LogRepositoryInterface
     *
     * @var LogRepositoryInterface $logRepository
     */
    protected $logRepository;
    /**
     * This variable contains a CollectionFactory
     *
     * @var CollectionFactory $collectionFactory
     */
    protected $collectionFactory;

    /**
     * MassDelete constructor
     *
     * @param Action\Context $context
     * @param LogRepositoryInterface $logRepository
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        LogRepositoryInterface $logRepository,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);

        $this->logRepository = $logRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Masse delete logs
     *
     * @return Redirect
     */
    public function execute()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        /** @var mixed $logIds */
        $logIds = $this->getRequest()->getParam('log_ids');
        $collection->addFieldToFilter('log_id', $logIds);
        $collectionSize = $collection->getSize();

        foreach ($collection as $log) {
            $this->logRepository->delete($log);
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        return $resultRedirect;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Pimgento_Api::pimgento_log');
    }
}
