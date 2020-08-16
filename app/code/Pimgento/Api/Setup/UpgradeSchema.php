<?php

namespace Pimgento\Api\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class UpgradeSchema
 *
 * @category  Class
 * @package   Pimgento\Api\Setup
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2018 Agence Dn'D
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.pimgento.com/
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var SchemaSetupInterface $installer */
        $installer = $setup;

        $installer->startSetup();

        $setup->getConnection()->addIndex(
            $installer->getTable('eav_attribute_option_value'),
            $installer->getIdxName(
                'eav_attribute_option_value',
                ['option_id', 'store_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['option_id', 'store_id'],
            AdapterInterface::INDEX_TYPE_UNIQUE
        );

        $installer->endSetup();
    }
}
