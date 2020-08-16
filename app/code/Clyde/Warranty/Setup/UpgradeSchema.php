<?php
namespace Clyde\Warranty\Setup;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class UpgradeSchema implements UpgradeSchemaInterface
{
    private $pageFactory;
    
    private $blockFactory;

    private $eavSetupFactory;

    private $_resourceConfig;
   
    public function __construct(
        PageFactory $pageFactory, 
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\App\State $state
    ) {
        $this->pageFactory = $pageFactory;
        $this->blockFactory = $blockFactory;
        $this->_resourceConfig = $resourceConfig;
        $state->setAreaCode('frontend');
    }
    
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
                $installer = $setup;
            $installer->startSetup();
            $table = $installer->getConnection()->newTable(
                $installer->getTable('clyde_customerwarranty')
            )
            ->addColumn(
                'customerwarranty_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
                'customerwarranty_id'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('nullable' => false),
                'customer_id'
            )
            ->addColumn(
                'plan_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('nullable' => false),
                'plan_id'
            )
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('nullable' => false),
                'order_id'
            )
            ->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('nullable' => false),
                'item_id'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('nullable' => false),
                'product_id'
            )
            
            ->addColumn(
                'customer_cost',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '10,2',
                array('nullable' => true),
                'Customer Cost'
            )
            ->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                array('nullable' => false),
                'created_time'
            )
            ->setComment(
                'Clyde Customer Warranty'
            );
        
            $installer->getConnection()->createTable($table);
                $eavTable1 = $installer->getTable('quote_item');
                $columns = array(
                    'warranty_info' => array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'warranty_info',
                    ),
                );
                $connection = $installer->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($eavTable1, $name, $definition);
                }

                $eavTable1 = $installer->getTable('sales_order_item');
                $columns1 = array(
                    'warranty_info' => array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'warranty_info',
                    ),
                );
                $connection1 = $installer->getConnection();
                foreach ($columns1 as $name1 => $definition1) {
                    $connection1->addColumn($eavTable1, $name1, $definition1);
                }
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $installer = $setup;
            $installer->startSetup();
            $table = $installer->getConnection()->newTable(
                $installer->getTable('clyde_warranty_products')
            )->addColumn(
                'items_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                array( 'identity' => true, 'nullable' => false, 'primary' => true ),
                'Items Id'
            )->addColumn(
                'warranty_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                255,
                array( 'nullable' => false ),
                'Warranty Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array( 'nullable' => false ),
                'Product Id'
            )->setComment(
                'Clyde Warranty clyde_warranty_products'
            );
                
                $installer->getConnection()->createTable($table);
        }
        
        
        
         if (version_compare($context->getVersion(), '1.1.3') < 0) {
            $installer = $setup;
            $installer->startSetup();
            $installer->run('create table warranty_sales(id int not null auto_increment, order_id varchar(100), contract_sale_id varchar(100), primary key(id))');
         }

        if (version_compare($context->getVersion(), '1.1.4') < 0) {
            $installer = $setup;
            $installer->startSetup();
            $installer->getConnection()->addColumn(
                $installer->getTable('warranty_sales'),
                'status',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Status'
                )
            );
        }

        if (version_compare($context->getVersion(), '1.1.5') < 0) {
            $installer = $setup;
            $installer->startSetup();
            $installer->getConnection()->addColumn(
                $installer->getTable('warranty_sales'),
                'shipment_id',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Shipment Id'
                )
            );
        }
        


        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $installer = $setup;
            $installer->startSetup();
            $table_product_sync = $installer->getConnection()->newTable(
                $installer->getTable('clyde_product_sync')
            )->addColumn(
                'product_sync_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array( 'identity' => true, 'nullable' => false, 'primary' => true ),
                'Items Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                255,
                array( 'nullable' => false ),
                'Product Id'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                array(),
                'Product SKU'
            )->addColumn(
                'clyde_product_json',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                array(),
                'Product json'
            )->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                array('nullable' => false),
                'created_time'
            )->addColumn(
                'updated_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                array('nullable' => false),
                'updated_time'
            )->setComment(
                'Clyde Warranty clyde_warranty_plan_assign'
            );
            
            $installer->getConnection()->createTable($table_product_sync);
        }

        if (version_compare($context->getVersion(), '1.2.11') < 0) {
            $installer = $setup;
            $installer->startSetup();
            
            $installer->getConnection()->addColumn(
                $installer->getTable('warranty_sales'),
                'refunded',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Refunded'
                )
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('warranty_sales'),
                'status_comment',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Status Comment'
                )
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('warranty_sales'),
                'contract_date',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Contract Date'
                )
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('warranty_sales'),
                'refunded_date',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Refunded Date'
                )
            );
        }

        if (version_compare($context->getVersion(), '1.2.12') < 0) {
            $installer = $setup;
            $installer->startSetup();
            
            $installer->getConnection()->addColumn(
                $installer->getTable('warranty_sales'),
                'processed_itemids',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Processed Item Ids'
                )
            );
        }

        if (version_compare($context->getVersion(), '1.2.13') < 0) {
            $installer = $setup;
            $installer->startSetup();
            
            $installer->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'item_base_price',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'    => '12,4',
                    'nullable' => true,
                    'default' => 0.0000,
                    'comment' => 'Item Base Price'
                )
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'warranty_base_price',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'    => '12,4',
                    'nullable' => true,
                    'default' => 0.0000,
                    'comment' => 'Warranty Base Price'
                )
            );
        }

        if (version_compare($context->getVersion(), '1.2.32') < 0) {
            $installer = $setup;
            $installer->startSetup();
            $installer->getConnection()->addColumn(
                $installer->getTable('clyde_warranty_products'),
                'condition_rule_type',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'condition_rule_type'
                )
            );
        }

        if (version_compare($context->getVersion(), '1.2.33', '<')) {
              $installer = $setup;
              $installer->startSetup();
              $tableName = $installer->getTable('clyde_warranty_products');
              $syncProduct = $installer->getTable('clyde_product_sync');
              if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection->modifyColumn(
                    $tableName,
                    'items_id',
                    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'length'    => '11', 'auto_increment' => true, 'nullable' => false),
                    'items Id'
                );
                $connection->modifyColumn(
                    $tableName,
                    'warranty_id',
                    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'length'    => '11', 'nullable' => false, 'default' => 0),
                    'Warranty Id'
                );
                $connection->modifyColumn(
                    $tableName,
                    'product_id',
                    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'length'    => '11', 'nullable' => false, 'default' => 0),
                    'Product Id'
                );
                /* clyde_product_sync */
                $connection->modifyColumn(
                    $syncProduct,
                    'product_sync_id',
                    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'length'    => '11', 'auto_increment' => true, 'nullable' => false),
                    'Product sync id'
                );
                $connection->modifyColumn(
                    $syncProduct,
                    'product_id',
                    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'length'    => '11', 'nullable' => false, 'default' => 0),
                    'Product Id'
                );
                // Changes here.
              }
        }

        if (version_compare($context->getVersion(), '1.2.34', '<')) {
            $installer = $setup;
            $installer->startSetup();
            $table_order_sync = $installer->getConnection()->newTable(
                $installer->getTable('clyde_order_sync')
            )->addColumn(
                'order_sync_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array( 'identity' => true, 'length'    => '11', 'nullable' => false, 'primary' => true ),
                'Items Id'
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                255,
                array( 'nullable' => false, 'length'    => '11' ),
                'Order Id'
            )->addColumn(
                'shipment_increment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                array( 'nullable' => false ),
                'Shipment Increment id'
            )->addColumn(
                'clyde_order_json',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                array(),
                'Product json'
            )->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                array('nullable' => false),
                'created_time'
            )->addColumn(
                'updated_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                array('nullable' => false),
                'updated_time'
            )->setComment(
                'Clyde Warranty'
            );
            
            $installer->getConnection()->createTable($table_order_sync);
        }

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->_resourceConfig->deleteConfig('clyde_warranty/product_sync_cron/frequency_product',\Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,0);
            $this->_resourceConfig->deleteConfig('clyde_warranty/product_sync_cron/time',\Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,0);
            $this->_resourceConfig->deleteConfig('clyde_warranty/clyde_order_sync/frequency_plan',\Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,0);
            $this->_resourceConfig->deleteConfig('clyde_warranty/clyde_order_sync/time',\Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,0);
            $this->_resourceConfig->deleteConfig('crontab/default/jobs/clyde_product_sync/schedule/cron_expr',\Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,0);
            $this->_resourceConfig->deleteConfig('crontab/default/jobs/clyde_order_sync/schedule/cron_expr',\Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,0);
            $this->_resourceConfig->deleteConfig('crontab/default/jobs/clyde_product_sync/run/model',\Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,0);
            $this->_resourceConfig->deleteConfig('crontab/default/jobs/clyde_order_sync/run/model',\Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,0);
        }

        if (version_compare($context->getVersion(), '2.0.11') < 0) {
            $installer = $setup;
            $installer->startSetup();
            
            $installer->getConnection()->addColumn(
                $installer->getTable('warranty_sales'),
                'item_id',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Added Item Id'
                )
            );
        }

        $setup->endSetup();
    }
}
