<?php
namespace Clyde\Warranty\Block\Adminhtml\Customer\Edit\Tab\Grid\Renderer;

class Warrantyinfo extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
      $data = $this->_helper->decryptString($row->getData($this->getColumn()->getIndex()));
      $html = '';
      $_warrantytype = $this->warrantytype->getOptionArray();
      if (isset($data['name']) &&  $data['name'] != '') {
        $html .= '<p><b>'.__('Name').' : </b>'.$data['name'].'</p>';
      }

      if(isset($data['sku']) &&  $data['sku'] != ''){
          $html .= '<p><b>'.__('Plan SKU').' : </b>'.$data['sku'].'</p>';
      }

      if (isset($data['warranty_type']) &&  $data['warranty_type'] != '') {
        $html .= '<p><b>'.__('Type').' : </b>'.__($_warrantytype[$data['warranty_type']]).'</p>';
        if ($data['warranty_type'] == \Clyde\Warranty\Model\Warranty\Warrantytype::FIXED){
          $html .= '<p><p><b>'.__('Amount').' : </b>'.$this->_currency->currency($data['customer_cost']).'</p>';
        } elseif ($data['warranty_type'] == \Clyde\Warranty\Model\Warranty\Warrantytype::PERCENT && isset($data['product_price']) && isset($data['warranty_applied_price'])) {
          if(isset($data['rule_product_price'])){
            $html .= '<p><b>'.__('Amount').' : </b>'.__('%1 of %2 = %3', $data['customer_cost'].'%', $this->_currency->currency($data['rule_product_price']), $this->_currency->currency($data['warranty_applied_price'])).'</p>';
          }
        }
      }

      if($row->getData('created_time')){
        $html .= $this->calulateWarrantyPeriod($row->getData('created_time'), $data);
      }

      return $html;
    }

    public function calulateWarrantyPeriod($created_time , $data )
    {
         $purchased = explode(" ", $created_time);
         $current_date = time(); //strtotime('2019-1-28');
         $purchased_date = strtotime($purchased['0']);
         $date_diff = $current_date - $purchased_date;
         $formated_date = round($date_diff / (60 * 60 * 24));
         if(isset($data['year_term']) && $data['year_term'] > 0) {
               $remaining_days = $data['year_term'] - $formated_date;
               if($remaining_days >= 0)
                {
                    $class = 'remaining';
               }
               else
               {
                    $class = 'expired';
               } 
         } else {
              $class = 'remaining';
         }

         return '<p><b>'.__('Purchased Date').' : </b><span class="'.$class.'">'.__($purchased['0']).'</span></p>';
    }
}
