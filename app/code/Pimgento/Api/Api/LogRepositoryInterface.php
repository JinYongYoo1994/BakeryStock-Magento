<?php

namespace Pimgento\Api\Api;

use Pimgento\Api\Api\Data\LogInterface;

/**
 * Interface LogRepositoryInterface
 *
 * @category  Interface
 * @package   Pimgento\Api\Api
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
interface LogRepositoryInterface
{
    /**
     * Retrieve a log by its id
     *
     * @param int $id
     *
     * @return LogInterface
     */
    public function get($id);

    /**
     * Retrieve a log by its identifier
     *
     * @param string $identifier
     *
     * @return LogInterface
     */
    public function getByIdentifier($identifier);

    /**
     * Save log object
     *
     * @param LogInterface $log
     *
     * @return $this
     */
    public function save(LogInterface $log);

    /**
     * Delete a log object
     *
     * @param LogInterface $log
     *
     * @return $this
     */
    public function delete(LogInterface $log);
}
