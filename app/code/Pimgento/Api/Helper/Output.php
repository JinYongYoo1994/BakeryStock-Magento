<?php

namespace Pimgento\Api\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Output
 *
 * @category  Class
 * @package   Pimgento\Api\Helper
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class Output extends AbstractHelper
{

    /**
     * This constant contains a string
     *
     * @var string PREFIX_DATE_FORMAT
     */
    const PREFIX_DATE_FORMAT = 'H:i:s';
    /**
     * This variable contains a DateTime
     *
     * @var DateTime $datetime
     */
    private $datetime;

    /**
     * Output constructor.
     *
     * @param Context $context
     * @param DateTime $datetime
     */
    public function __construct(
        Context $context,
        DateTime $datetime
    ) {
        parent::__construct($context);

        $this->datetime = $datetime;
    }

    /**
     * Send an error when import code is not found
     *
     * @return array
     */
    public function getNoImportFoundResponse()
    {
        /** @var string $prefix */
        $prefix = $this->getPrefix();

        /** @var array $response */
        $response = [
            'comment'    => __('%1 Start import', $prefix),
            'continue'   => false,
            'identifier' => '',
            'message'    => __('%1 Import code not found', $prefix),
            'next'       => '',
            'status'     => false,
        ];

        return $response;
    }

    /**
     * Get output message prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        /** @var string $date */
        $date = $this->datetime->gmtDate(self::PREFIX_DATE_FORMAT);

        return '[' . $date . '] ';
    }

    /**
     * Send an error if import is already running
     *
     * @return array
     */
    public function getImportAlreadyRunningResponse()
    {
        /** @var string $prefix */
        $prefix = $this->getPrefix();

        /** @var array $response */
        $response = [
            'comment'    => __('%1 Start import', $prefix),
            'continue'   => false,
            'identifier' => '',
            'message'    => __('%1 Import already running', $prefix),
            'next'       => '',
            'status'     => false,
        ];

        return $response;
    }

    /**
     * Send Api Connexion error
     *
     * @return array
     */
    public function getApiConnectionError()
    {
        /** @var string $prefix */
        $prefix = $this->getPrefix();

        /** @var array $response */
        $response = [
            'comment'    => __('%1 Start import', $prefix),
            'continue'   => false,
            'identifier' => '',
            'message'    => __('PIM API connection error', $prefix),
            'next'       => '',
            'status'     => false,
        ];

        return $response;
    }
}
