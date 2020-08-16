<?php

namespace Clyde\Warranty\Block\Adminhtml\Warrantysale\Edit\Tab\Renderer;
use Magento\Framework\DataObject;
/**
 * Warrantysale edit form clyderefunded tab
 */
class Clyderefunded extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    
    public function render(DataObject $row)
    {
        if($row->getStatusComment()){
            $html = '';
            if($row->getContractDate()){
                $html .= '<strong>Contract Active: </strong> '.date('d F Y g:i a', strtotime($row->getContractDate()));
            }

            if($row->getRefundedDate()){
                $html .= '<br><strong>Refunded at: </strong> '.date('d F Y g:i a', strtotime($row->getRefundedDate()));
            }

            if(strtolower(trim($row->getStatus())) == strtolower('Failure')){
                $data = json_decode($row->getStatusComment(), true);
                $title = isset($data['title'])?$data['title']:'';
                $detail = isset($data['detail'])?' - '.$data['detail']:'';
                $html .= '<strong>Error: </strong> '.$title.$detail;
            }

            return $html;
        }

        return "";
    }
}
