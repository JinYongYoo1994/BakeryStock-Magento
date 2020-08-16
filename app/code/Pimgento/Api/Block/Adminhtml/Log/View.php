<?php

namespace Pimgento\Api\Block\Adminhtml\Log;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\UrlFactory;
use Magento\Backend\Model\UrlInterface;
use Pimgento\Api\Api\Data\LogInterface;
use Pimgento\Api\Api\LogRepositoryInterface;
use Pimgento\Api\Model\Log as LogModel;

/**
 * Class View
 *
 * @category  Class
 * @package   Pimgento\Api\Block\Adminhtml\Log
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class View extends Template
{
    /**
     * Model Url instance
     *
     * @var UrlInterface $urlModel
     */
    protected $urlModel;
    /**
     * This variable contains a LogRepository
     *
     * @var LogRepositoryInterface $logRepository
     */
    protected $logRepository;

    /**
     * View constructor
     *
     * @param LogRepositoryInterface $logRepository
     * @param UrlFactory $backendUrlFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        LogRepositoryInterface $logRepository,
        UrlFactory $backendUrlFactory,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->urlModel = $backendUrlFactory->create();
        $this->logRepository = $logRepository;
    }

    /**
     * Retrieve log
     *
     * @return LogModel
     */
    public function getLog()
    {
        return $this->logRepository->get($this->getLogId());
    }

    /**
     * Retrieve steps
     *
     * @return array
     */
    public function getSteps()
    {
        /** @var array $steps */
        $steps = [];
        /** @var LogInterface $log */
        $log = $this->getLog();

        if ($log->hasData()) {
            $steps = $log->getSteps();
        }

        return $steps;
    }

    /**
     * Retrieve log id
     *
     * @return int
     */
    public function getLogId()
    {
        return $this->getData('log_id');
    }

    /**
     * Retrieve back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->urlModel->getUrl('pimgento_api/log');
    }

    /**
     * Set log id
     *
     * @param int $logId
     *
     * @return $this
     */
    public function setLogId($logId)
    {
        $this->setData('log_id', $logId);

        return $this;
    }
}
