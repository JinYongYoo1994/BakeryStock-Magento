<?php

namespace Pimgento\Api\Api;

use Magento\Framework\DataObject;
use Pimgento\Api\Api\Data\ImportInterface;

/**
 * Interface ImportRepositoryInterface
 *
 * @category  Interface
 * @package   Pimgento\Api\Api
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
interface ImportRepositoryInterface
{

    /**
     * Description add function
     *
     * @param DataObject $import
     *
     * @return void
     */
    public function add(DataObject $import);

    /**
     * Description getByCode function
     *
     * @param string $code
     *
     * @return ImportInterface
     */
    public function getByCode($code);

    /**
     * Description getList function
     *
     * @return Iterable
     */
    public function getList();

    /**
     * Description deleteByCode function
     *
     * @param string $code
     *
     * @return void
     */
    public function deleteByCode($code);
}
