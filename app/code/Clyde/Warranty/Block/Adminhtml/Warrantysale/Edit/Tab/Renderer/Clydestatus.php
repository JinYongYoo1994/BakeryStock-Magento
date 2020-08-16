<?php

namespace Clyde\Warranty\Block\Adminhtml\Warrantysale\Edit\Tab\Renderer;
use Magento\Framework\DataObject;
/**
 * Warrantysale edit form clydestatus tab
 */
class Clydestatus extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $urlBuider;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }
    public function render(DataObject $row)
    {
        $mageCateId = $row->getStatus();
        if($row->getStatus()=="Success" || $row->getStatus()=="Cancel")
        {
            $color = ($row->getStatus()=="Success")?'green':'red';
            return '<span style="color:'.$color.'">'.$row->getStatus().'</span>';
        }else{
            $color = '#FF7F50';
             return '<span style="color:'.$color.'">'."Failure <a href='".$this->urlBuilder->getUrl('warranty/index/clyde', array('_query' => array('id' => $row->getShipmentId(), 'return_url' => '2')))."'> (Try Again)</a>".'</span>';
        }
        
    }
}
