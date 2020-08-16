<?php

namespace Pimgento\Api\Job;

use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Data\CollectionFactory as CollectionFactory;
use Pimgento\Api\Api\Data\ImportInterface;
use Pimgento\Api\Api\ImportRepositoryInterface;
use Pimgento\Api\Helper\Config as ConfigHelper;

/**
 * Class ImportRepository
 *
 * @category  Class
 * @package   Pimgento\Api\Job
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class ImportRepository implements ImportRepositoryInterface
{

    /**
     * This variable contains an EntityFactoryInterface
     *
     * @var EntityFactoryInterface $entityFactory
     */
    private $entityFactory;
    /**
     * This variable contains a Collection
     *
     * @var Collection $collection
     */
    private $collection;
    /**
     * This variable contains a ConfigHelper
     *
     * @var ConfigHelper $configHelper
     */
    protected $configHelper;

    /**
     * ImportRepository constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param CollectionFactory $collectionFactory
     * @param ConfigHelper $configHelper
     * @param array $data
     *
     * @throws \Exception
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        CollectionFactory $collectionFactory,
        ConfigHelper $configHelper,
        $data = []
    ) {
        $this->entityFactory = $entityFactory;
        $this->collection    = $collectionFactory->create();
        $this->configHelper  = $configHelper;

        $this->initCollection($data);
    }

    /**
     * Load available imports
     *
     * @param array $data
     *
     * @return void
     * @throws \Exception
     */
    private function initCollection($data)
    {
        foreach ($data as $id => $class) {
            if (!class_exists($class)) {
                continue;
            }

            /** @var Import $import */
            $import = $this->entityFactory->create($class);

            if (!$this->configHelper->isAkeneoEnterprise() && $import->isImportEnterprise()) {
                continue;
            }

            $import->setData('id', $id);
            $this->add($import);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add(DataObject $import)
    {
        $this->collection->addItem($import);
    }

    /**
     * {@inheritdoc}
     */
    public function getByCode($code)
    {
        /** @var ImportInterface $import */
        $import = $this->collection->getItemById($code);

        return $import;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCode($code)
    {
        $this->collection->removeItemByKey($code);
    }
}
