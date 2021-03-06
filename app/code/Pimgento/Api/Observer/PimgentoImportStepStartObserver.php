<?php

namespace Pimgento\Api\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Pimgento\Api\Api\Data\ImportInterface;
use Pimgento\Api\Api\LogRepositoryInterface;
use Pimgento\Api\Job\Import;
use Pimgento\Api\Model\Log as LogModel;
use Pimgento\Api\Model\LogFactory;

/**
 * Class PimgentoImportStepStartObserver
 *
 * @category  Class
 * @package   Pimgento\Api\Observer
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class PimgentoImportStepStartObserver implements ObserverInterface
{
    /**
     * This variable contains a LogFactory
     *
     * @var LogFactory $logFactory
     */
    protected $logFactory;
    /**
     * This variable contains a LogRepositoryInterface
     *
     * @var LogRepositoryInterface $logRepository
     */
    protected $logRepository;

    /**
     * PimgentoImportStepStartObserver constructor
     *
     * @param LogFactory $logFactory
     * @param LogRepositoryInterface $logRepository
     */
    public function __construct(
        LogFactory $logFactory,
        LogRepositoryInterface $logRepository
    ) {
        $this->logFactory = $logFactory;
        $this->logRepository = $logRepository;
    }

    /**
     * Log start of the step
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Import $import */
        $import = $observer->getEvent()->getImport();

        if ($import->getStep() == 0) {
            /** @var LogModel $log */
            $log = $this->logFactory->create();
            $log->setIdentifier($import->getIdentifier());
            $log->setCode($import->getCode());
            $log->setName($import->getName());
            $log->setStatus(ImportInterface::IMPORT_PROCESSING); // processing
            $this->logRepository->save($log);
        } else {
            $log = $this->logRepository->getByIdentifier($import->getIdentifier());
        }

        if ($log->hasData()) {
            $log->addStep(
                [
                    'log_id' => $log->getId(),
                    'identifier' => $import->getIdentifier(),
                    'number' => $import->getStep(),
                    'method' => $import->getMethod(),
                    'message' => $import->getComment(),
                ]
            );
        }

        return $this;
    }
}
