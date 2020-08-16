<?php
namespace Clyde\Warranty\Block\Adminhtml\Warrantysale;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Clyde\Warranty\Model\warrantysaleFactory
     */
    protected $_warrantysaleFactory;

    /**
     * @var \Clyde\Warranty\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Clyde\Warranty\Model\warrantysaleFactory $warrantysaleFactory
     * @param \Clyde\Warranty\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Clyde\Warranty\Model\WarrantysaleFactory $WarrantysaleFactory,
        \Clyde\Warranty\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = array()
    ) {
        $this->_warrantysaleFactory = $WarrantysaleFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_warrantysaleFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            )
        );
        $this->addColumn(
            'order_id',
            array(
                'header' => __('Order Id'),
                'type' => 'text',
                'index' => 'order_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            )
        );
        $this->addColumn(
            'shipment_id',
            array(
                'header' => __('Shipment Id'),
                'type' => 'text',
                'index' => 'shipment_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            )
        );
         $this->addColumn(
             'contract_sale_id',
             array(
                'header' => __('Contract Sale Id'),
                'type' => 'text',
                'index' => 'contract_sale_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
             )
         );
         $this->addColumn(
             'status',
             array(
                'header' => __('Status'),
                'type' => 'text',
                'index' => 'status',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer'  => 'Clyde\Warranty\Block\Adminhtml\Warrantysale\Edit\Tab\Renderer\Clydestatus'
             )
         );

        $this->addColumn(
            'refunded',
            array(
                'header' => __('Refunded'),
                'type' => 'options',
                'index' => 'refunded',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'options'  => array('0'=>'False','1'=>'True')
            )
        );

        $this->addColumn(
            'status_comment',
            array(
                'header' => __('Status Info'),
                'type' => 'text',
                'index' => 'status_comment',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer'  => 'Clyde\Warranty\Block\Adminhtml\Warrantysale\Edit\Tab\Renderer\Clyderefunded'
            )
        );
        
        
           $this->addExportType($this->getUrl('warranty/*/exportCsv', array('_current' => true)), __('CSV'));
           $this->addExportType($this->getUrl('warranty/*/exportExcel', array('_current' => true)), __('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('id');
        //$this->getMassactionBlock()->setTemplate('Clyde_Warranty::warrantysale/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('warrantysale');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => __('Delete'),
                'url' => $this->getUrl('warranty/*/massDelete'),
                'confirm' => __('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'tryagain',
            array(
                'label' => __('Try Again'),
                'url' => $this->getUrl('warranty/*/tryAgain'),
                'confirm' => __('Are you sure?')
            )
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label' => __('Change status'),
                'url' => $this->getUrl('warranty/*/massStatus', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    )
                )
            )
        );


        return $this;
    }
        

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('warranty/*/index', array('_current' => true));
    }

    /**
     * @param \Clyde\Warranty\Model\warrantysale|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        
        // return $this->getUrl(
        //     'warranty/*/edit',
        //     ['id' => $row->getId()]
        // );
        
    }

    

}