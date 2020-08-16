<?php

namespace Pimgento\Api\Model;

use Pimgento\Api\Api\Data\LogInterface;
use Pimgento\Api\Api\LogRepositoryInterface;
use Pimgento\Api\Model\ResourceModel\Log as LogResourceModel;

/**
 * Class LogRepository
 *
 * @category  Class
 * @package   Pimgento\Api\Model
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class LogRepository implements LogRepositoryInterface
{
    /**
     * This variable contains a LogResourceModel
     *
     * @var LogResourceModel $logResourceModel
     */
    protected $logResourceModel;
    /**
     * This variable contains a LogFactory
     *
     * @var LogFactory $logFactory
     */
    protected $logFactory;

    /**
     * LogRepository constructor
     *
     * @param LogResourceModel $logResourceModel
     * @param LogFactory $logFactory
     */
    public function __construct(
        LogResourceModel $logResourceModel,
        LogFactory $logFactory
    ) {
        $this->logResourceModel = $logResourceModel;
        $this->logFactory = $logFactory;
    }

    /**
     * Retrieve a log by its id
     *
     * @param int $id
     *
     * @return LogInterface
     */
    public function get($id)
    {
        /** @var LogInterface $log */
        $log = $this->logFactory->create();
        $this->logResourceModel->load($log, $id);

        return $log;
    }

    /**
     * Retrieve a log by its identifier
     *
     * @param string $identifier
     *
     * @return LogInterface
     */
    public function getByIdentifier($identifier)
    {
        /** @var LogInterface $log */
        $log = $this->logFactory->create();
        $this->logResourceModel->load($log, $identifier, LogInterface::IDENTIFIER);

        return $log;
    }

    /**
     * Save log object
     *
     * @param LogInterface $log
     *
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(LogInterface $log)
    {
        $this->logResourceModel->save($log);

        return $this;
    }

    /**
     * Delete a log object
     *
     * @param LogInterface $log
     *
     * @return $this
     */
    public function delete(LogInterface $log)
    {
        $this->logResourceModel->delete($log);

        return $this;
    }
}
