<?php

namespace Pimgento\Api\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Pimgento\Api\Api\Data\ImportInterface;
use Pimgento\Api\Api\Data\LogInterface;
use Pimgento\Api\Api\LogRepositoryInterface;

/**
 * Class PimgentoImportStepFinishObserver
 *
 * @category  Class
 * @package   Pimgento\Api\Observer
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class PimgentoImportStepFinishObserver implements ObserverInterface
{
    /**
     * This variable contains a LogRepositoryInterface
     *
     * @var LogRepositoryInterface $logRepository
     */
    protected $logRepository;

    /**
     * PimgentoImportStepFinishObserver constructor
     *
     * @param LogRepositoryInterface $logRepository
     */
    public function __construct(
        LogRepositoryInterface $logRepository
    ) {
        $this->logRepository = $logRepository;
    }

    /**
     * Log end of the step
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var $import ImportInterface */
        $import = $observer->getEvent()->getImport();
        /** @var LogInterface $log */
        $log = $this->logRepository->getByIdentifier($import->getIdentifier());

        if (!$log->hasData()) {
            return $this;
        }

        if ($import->getStep() + 1 == $import->countSteps()) {
            $log->setStatus(ImportInterface::IMPORT_SUCCESS); // Success
            $this->logRepository->save($log);
        }

        if ($import->isDone() && !$import->getStatus()) {
            $log->setStatus(ImportInterface::IMPORT_ERROR); // Error
            $this->logRepository->save($log);
        }

        $log->addStep(
            [
                'log_id' => $log->getId(),
                'identifier' => $import->getIdentifier(),
                'number' => $import->getStep(),
                'method' => $import->getMethod(),
                'message' => $import->getMessage(),
                'continue' => $import->isDone() ? 0 : 1,
                'status' => $import->getStatus() ? 1 : 0,
            ]
        );

        return $this;
    }
}
