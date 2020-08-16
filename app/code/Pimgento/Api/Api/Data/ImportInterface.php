<?php

namespace Pimgento\Api\Api\Data;

/**
 * Interface ImportInterface
 *
 * @category  Interface
 * @package   Pimgento\Api\Api\Data
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
interface ImportInterface
{
    /**
     * @var int IMPORT_SUCCESS
     */
    const IMPORT_SUCCESS = 1;
    /**
     * @var int IMPORT_ERROR
     */
    const IMPORT_ERROR = 2;
    /**
     * @var int IMPORT_PROCESSING
     */
    const IMPORT_PROCESSING = 3;
}
