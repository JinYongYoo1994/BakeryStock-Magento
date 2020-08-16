<?php
namespace Clyde\Warranty\Block\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Attributes extends AbstractFieldArray
{
   
    protected $_addAfter = false;

    protected $_attribute;

    protected $_addButtonLabel = false;

    protected function _extendedAtributes()
    {
        if (!$this->_attribute) {
            $this->_attribute = $this->getLayout()->createBlock(
                \Clyde\Warranty\Block\Adminhtml\Form\Field\Attributes::class,
                '',
                array('data' => array('is_render_to_js_template' => true))
            );
            $this->_attribute->setClass('customer_group_select');
        }

        return $this->_attribute;
    }

    protected function _prepareToRender()
    {
        $this->addColumn(
            'attributes',
            array('label' => __('Extended Atributes'),  'class' => 'required-entry' ,'renderer' => $this->_extendedAtributes())
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = 'Add Attribute';
    }

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = array();
        $optionExtraAttr['option_' . $this->_extendedAtributes()->calcOptionHash($row->getData('attributes'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
