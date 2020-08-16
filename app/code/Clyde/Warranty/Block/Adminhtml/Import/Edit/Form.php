<?php
namespace Clyde\Warranty\Block\Adminhtml\Import\Edit;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Backend\Block\Widget\Form\Generic;
 
class Form extends Generic
{
 
    protected $_systemStore;
    protected $_filesystem;
    protected $storeManager;
 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Filesystem $filesystem,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->_filesystem = $filesystem;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    protected function _construct()
    {
        parent::_construct();
        $this->setId('import_form');
        $this->setTitle(__('Warranty Plan Import'));
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('warranty/logs.phtml');
        return $this;
    }

    protected function _prepareForm()
    {
 
        $form = $this->_formFactory->create(
            array('data' => array('id' => 'edit_form', 'action' => $this->getUrl('warranty/plan/importsave'), 'method' => 'post', 'enctype' => 'multipart/form-data'))
        );
 
        $form->setHtmlIdPrefix('import_');
 
        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend' => __('Import Plans'), 'class' => 'fieldset-wide')
        );
 
        $fieldset->addField(
            'plan_field_name',
            'file',
            array(
                'name'        => 'plan_field_name',
                'label'       => __('Select File'),
                'title'       => __('Select File'),
                'required'    => true
            )
        );
        //$form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
 
        return parent::_prepareForm();
    }

    public function getMediaFileDirectory()
    {
        $fileDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $dirPath =  $fileDirectory . "synccsv/";
        return $dirPath;
    }

    public function getAllOrderLog()
    {
        $dirpath = $this->getMediaFileDirectory();
        $times = $this->getLastMonth(date('Y-m-d', strtotime("-1 months")), date('Y-m-d'));
        $fileArray = array();
        foreach($times as $date){
            if (file_exists($dirpath.$date)){ 
                $result = $this->getOrderLogFile($date);
                if(empty($result) !== true){
                    $fileArray[] = $result;
                }
            }
        }

        return $fileArray;
    }

    public function getLastMonth($start, $end, $format = 'Y-m-d') 
    { 
    
        $array = array(); 
          
        $interval = new \DateInterval('P1D'); 
      
        $realEnd = new \DateTime($end); 
        $realEnd->add($interval); 
      
        $period = new \DatePeriod(new \DateTime($start), $interval, $realEnd); 
      
        foreach($period as $date) {                  
            $array[] = $date->format($format);  
        } 
      
        return array_reverse($array); 
    } 
    /*public function getLastMonth() {
        $now = new \DateTime();
        $lastMonth = $now->sub(new \DateInterval('P1M'));
        for ($i = 1; $i <= 6; $i++) 
        {
           $months[] = date("Y-m%", strtotime( date( 'Y-m-01' )." -$i months"));
        }
        var_dump($months);
        return $lastMonth->format('Ym');
    }*/

    public function getProductLog()
    {
        $dirpath = $this->getMediaFileDirectory();
        //$times = array(date('Y-m-d'),date('Y-m-d',strtotime("-1 days")),date('Y-m-d',strtotime("-2 days")));
        $times = $this->getLastMonth(date('Y-m-d', strtotime("-1 months")), date('Y-m-d'));
        $fileArray = array();
        foreach($times as $date){
            if (file_exists($dirpath.$date)){ 
                $result = $this->getProductLogFile($date);
                if(empty($result) !== true){
                    $fileArray[] = $result;
                }
            }
        }

        return $fileArray;
    }

    public function getProductLogFile($date)
    {
        $dirpath = $this->getMediaFileDirectory();
        $fileArray = scandir($dirpath.$date);
        $files = array();
        if(count($fileArray) > 0){
            foreach ($fileArray as $key => $value) {
               if(strpos($value, 'product_import_on') !== false){
                    $filename = $dirpath.$date.'/'.$value;
                    $currentStore = $this->storeManager->getStore();
                    $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'synccsv/'.$date.'/'.$value;
                    $item = array('filename'=>$value, 'filepath'=>$mediaUrl,'fileCreated'=>date("F d, Y h:i:s A", filemtime($filename)));
                    $files=$item;
               }
            }
        }

        return $files;
    }
    
    public function getOrderLogFile($date)
    {
        $dirpath = $this->getMediaFileDirectory();
        $fileArray = scandir($dirpath.$date);
        $files = array();
        if(count($fileArray) > 0){
            foreach ($fileArray as $key => $value) {
               if(strpos($value, 'order_sync_on') !== false){
                    $filename = $dirpath.$date.'/'.$value;
                    $currentStore = $this->storeManager->getStore();
                    $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'synccsv/'.$date.'/'.$value;
                    $item = array('filename'=>$value, 'filepath'=>$mediaUrl,'fileCreated'=>date("F d, Y h:i:s A", filemtime($filename)));
                    $files=$item;
               }
            }
        }

        return $files;
    }
}