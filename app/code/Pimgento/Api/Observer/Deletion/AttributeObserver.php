<?php

namespace Pimgento\Api\Observer\Deletion;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Pimgento\Api\Helper\Import\Entities;
use Pimgento\Api\Job\Attribute as ImportJob;

/**
 * Class AttributeObserver
 *
 * @category  Class
 * @package   Pimgento\Api\Observer\Deletion
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class AttributeObserver implements ObserverInterface
{
    /**
     * This variable contains an Entities
     *
     * @var Entities $entities
     */
    protected $entities;
    /**
     * This variable contains an Attribute
     *
     * @var ImportJob $job
     */
    protected $job;

    /**
     * AttributeObserver Constructor
     *
     * @param Entities $entities
     * @param ImportJob $job
     */
    public function __construct(
        Entities $entities,
        ImportJob $job
    ) {
        $this->entities = $entities;
        $this->job      = $job;
    }
    /**
     * Remove entity relation
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var $attribute \Magento\Eav\Model\Entity\Attribute */
        $attribute = $observer->getEvent()->getAttribute();

        $this->entities->delete($this->job->getCode(), $attribute->getId());
    }
}
