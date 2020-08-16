<?php

namespace Pimgento\Api\Converter;

use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;

/**
 * Class ArrayToJsonResponseConverter
 *
 * @category  Class
 * @package   Pimgento\Api\Converter
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class ArrayToJsonResponseConverter
{

    /**
     * This variable contains a ResultJsonFactory
     *
     * @var ResultJsonFactory $resultJsonFactory
     */
    private $resultJsonFactory;

    /**
     * ArrayToJsonResponseConverter constructor.
     *
     * @param ResultJsonFactory $resultJsonFactory
     */
    public function __construct(
        ResultJsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * This function convert an array to json
     *
     * @param array $data
     *
     * @return ResultJson
     */
    public function convert(array $data)
    {
        /** @var ResultJson $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData($data);
    }
}
