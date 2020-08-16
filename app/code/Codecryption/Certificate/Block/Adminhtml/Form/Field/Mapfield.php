<?php

namespace Codecryption\Certificate\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Mapfield extends AbstractFieldArray
{
    /**
     * {@inheritdoc}
     */
    protected $_addAfter = false;

    protected $_groupRenderer;

    protected $_groupCmsRenderer;
    /**
     * Label of add button
     *
     * @var string
     */
    protected $_addButtonLabel = false;

    protected function _getAttributeRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                \Codecryption\Certificate\Block\Adminhtml\Form\Field\Attribute::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            //$this->_groupRenderer->setClass('dropdown_group_select');
        }
        return $this->_groupRenderer;
    }

    protected function _getCmsRenderer()
    {
        if (!$this->_groupCmsRenderer) {
            $this->_groupCmsRenderer = $this->getLayout()->createBlock(
                \Codecryption\Certificate\Block\Adminhtml\Form\Field\Cms::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            //$this->_groupRenderer->setClass('dropdown_group_select');
        }
        return $this->_groupCmsRenderer;
    }

    protected function _prepareToRender()
    {
        /*$this->addColumn('attribute_field', ['label' => __('Product Attribute'), 'class' => 'required-entry']);
        $this->addColumn('cms_block', ['label' => __('Cms Block'), 'class' => 'required-entry']);*/
        $this->addColumn(
            'attribute_field',
            ['label' => __('Product Attribute'),'size' => '130px',
                'renderer' => $this->_getAttributeRenderer(), 'class' => 'required-entry']
        );
        $this->addColumn(
            'cms_block',
            ['label' => __('Cms Block'),'size' => '130px',
                'renderer' => $this->_getCmsRenderer(), 'class' => 'required-entry']
        );
        
        $this->_addAfter = false;
        $this->_addButtonLabel = 'Add';
    }

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getAttributeRenderer()->calcOptionHash($row->getData('attribute_field'))] =
            'selected="selected"';
        $optionExtraAttr['option_' . $this->_getCmsRenderer()->calcOptionHash($row->getData('cms_block'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}