<?php
namespace Clyde\Warranty\Block\Adminhtml\Productwarranty;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $moduleManager;
   
    protected $_setsFactory;
    
    protected $_registry;
   
    protected $_productFactory;
    
    protected $_type;

    protected $_status;
    
    protected $_collectionFactory;

    protected $_visibility;
    
    protected $_statusFactory;
    
    protected $_warrantytypeFactory;
    
    protected $_productCollectionFactory;
    
    protected $_websiteFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Clyde\Warranty\Model\ResourceModel\Warranty\Collection $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Clyde\Warranty\Model\Status $statusCollection,
        \Clyde\Warranty\Model\Warranty\Warrantytype $warrantytypeCollection, 
        \Clyde\Warranty\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->moduleManager = $moduleManager;
        $this->_statusFactory = $statusCollection;
        $this->_warrantytypeFactory = $warrantytypeCollection;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }
   
    protected function _construct()
    {
        parent::_construct();
        
        $this->setId('productGrid');
        $this->setDefaultSort('warranty_id');
        $this->setFilterVisibility(false);
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
       
    }
    
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }
   
    protected function _prepareCollection()
    {
        $productId = $this->getRequest()->getParam('id');
        try{
            $collection =$this->_collectionFactory->load();
            $collection->getSelect()->join(
                array('warranty_product' => $collection->getTable('clyde_warranty_products')),
                'main_table.warranty_id = warranty_product.warranty_id',
                array('product_id')
            );
            $collection->addFieldToFilter('product_id', $productId);
            $collection->addFieldToFilter('products', \Clyde\Warranty\Model\Warranty\Products::FOR_SPECIFIC);
            $this->setCollection($collection);
            parent::_prepareCollection();
            return $this;
        }
        catch(\Exception $e)
        {
            throw new \Magento\Framework\Validator\Exception(__($e->getMessage()));
        }
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites',
                    'catalog_product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left'
                );
            }
        }

        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'warranty_id',
            array(
                'header' => __('Select Warranty'),
                'type' => 'checkbox',
                'index' => 'warranty_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                "field_name" => "warranty_id[]",
                "values" => $this->_getSelectedWarranty()
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'name'
            )
        );
        $this->addColumn(
            'warranty_type',
            array(
                'header' => __('Warranty Type'),
                'index' => 'warranty_type',
                'class' => 'warranty_type',
                'type' => 'options',
                'options' => $this->_warrantytypeFactory->getOptionArray()
            )
        );
        $this->addColumn(
            'warranty_amount',
            array(
                'header' => __('Warranty Amount'),
                'index' => 'warranty_amount',
                'class' => 'warranty_amount'
            )
        );
        $this->addColumn(
            'status',
            array(
                'header' => __('Status'),
                'index' => 'status',
                'class' => 'status',
                'type' => 'options',
                'options' => $this->_statusFactory->getOptionArray()
            )
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }
    protected function _getSelectedWarranty()
    {
        $warranty = $this->getWrranty();
        return  $warranty;
    }
    protected function getWrranty()
    {
        $warranty = array();
        $productId = $this->getRequest()->getParam('id');
        return $warranty;   
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('warranty_id');
        $this->getMassactionBlock()->setFormFieldName('warranty_id');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => __('Delete'),
                'url' => $this->getUrl('warranty/*/massDelete'),
                'confirm' => __('Are you sure?')
            )
        );
        return $this;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('warranty/*/index', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            'warranty/*/edit',
            array('store' => $this->getRequest()->getParam('store'), 'warranty_id' => $row->getId())
        );
    }
}
