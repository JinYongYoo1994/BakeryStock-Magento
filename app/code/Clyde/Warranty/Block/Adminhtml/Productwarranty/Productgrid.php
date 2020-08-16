<?php
namespace Clyde\Warranty\Block\Adminhtml\Productwarranty;

class Productgrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $moduleManager;
    
    protected $_setsFactory;
    
    protected $_productFactory;
    
    protected $_type;
    
    protected $_status;
   
    protected $_collectionFactory;
   
    protected $_visibility;
   
    protected $_websiteFactory;
    
    protected $_stickerid = array();
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Clyde\Warranty\Model\ResourceModel\Warranty\Collection $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = array()
    ) {
        
        $this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        
        $this->setId('productGrid');
        $this->setDefaultSort('sticker_id');
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
            $collection->addFieldToFilter('warranty_product.product_id', $productId);
            $collection->addFieldToFilter('products', \Clyde\Warranty\Model\Warranty\Products::FOR_SPECIFIC);
            $this->setCollection($collection);
            parent::_prepareCollection();
          
            return $this;
        }
        catch(\Exception $e)
        {
            throw new \Exception(__($e->getMessage()));
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
        $this->_stickerid = array("2");
        $this->addColumn(
            'warranty_id',
            array(
                'header' => __('Select Sticker'),
                'type' => 'checkbox',
                'index' => 'warranty_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                "field_name" => "warranty_id[]",
                "values" => $this->_getSelectedStickers()
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => __('Title'),
                'index' => 'name',
                'class' => 'title'
            )
        );
        $this->addColumn(
            'position',
            array(
                'header' => __('Position'),
                'index' => 'position',
                'class' => 'position'
            )
        );
        $this->addColumn(
            'active',
            array(
                'header' => __('Active'),
                'index' => 'active',
                'class' => 'active',
                'type'=>'options',
                'options' => array('1' => 'Yes', '0' => 'No')
            )
        );
       
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }
    protected function _getSelectedStickers()
    {
        $sticker = $this->getSticker();
        return  $sticker;
    }
    protected function getSticker()
    {
        $stickers = array();
        $productId = $this->getRequest()->getParam('id');
        return $stickers;   
    }
     
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('warranty_id');
        $this->getMassactionBlock()->setFormFieldName('warranty_id');
        return $this;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('warranty/*/edit', array('_current' => true));
    }
}
