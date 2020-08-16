<?php
namespace Clyde\Warranty\Block\Adminhtml\Customer\Edit\Tab\Grid\Renderer;

class Warrantyperiod extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $warrantytype;
    
    protected $_currency;

    public $_helper;
    
    public function __construct(\Magento\Backend\Block\Context $context, \Clyde\Warranty\Model\Warranty\Warrantytype $warrantytype,\Magento\Framework\Pricing\Helper\Data $currency, \Clyde\Warranty\Helper\Data $helper, array $data = array())
    {
        parent::__construct($context, $data);
        $this->warrantytype = $warrantytype;
        $this->_helper = $helper;
        $this->_currency = $currency;
    }
    
    public function render(\Magento\Framework\DataObject $row)
    {
      $data = $this->_helper->decryptString($row->getData('warranty_info'));
      $html = '';
      if(isset($data['year_term']) && $data['year_term'] > 0){
         $remaining_days = $this->calulateWarrantyPeriod($row->getData('created_time'), $data);
          if(isset($remaining_days['remaining'])) {
              $html = '<span class="remaining">'.$remaining_days['remaining'].'</td>';
          } else {
              $html = '<span class="expired">'.__('Expired').'</span>';
          }
      } else {
            $html = '<span class="remaining">'.__('Lifetime').'</span>';   
      }
 
      return $html;
    }

    public function calulateWarrantyPeriod($created_time , $data )
    {

         $purchased = explode(" ", $created_time);
         if(isset($data['year_term']) && $data['year_term'] > 0) {
                $start = date('Y-m-d');
                $dateString = $created_time;
                $t = strtotime($dateString);
                $t2 = strtotime('+'.$data['year_term'].' year', $t);
                $end = date('Y-m-d', $t2);
                $diff = strtotime($end) - strtotime($start);
                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                $dateString = '';//= sprintf("%d years, %d months, %d days", $years, $months, $days);
                if($years > 0){
                    $dateString .= $years.' years ';
                }

                if($months > 0){
                    $dateString .= $months.' months ';
                }

                if($days > 0){
                    $dateString .= $days.' days ';
                }

                $remaining_days = ($diff > 0) ? 1 : 0;

                if($years == 0 && $months == 0 && $days == 0){
                    $class = 'remaining';
                    return array('remaining'=>__('Today'));
                }elseif($remaining_days == 1){
                    $class = 'remaining';
                    return array('remaining'=>$dateString);
                }
                else
                {
                    $class = 'expired';
                    return array('expired'=>$dateString);
                } 
         }

    }
}
