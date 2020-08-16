<?php

namespace Codecryption\Certificate\Block\Adminhtml\Form\Field;

class Attribute extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Model Enabledisable
     *
     * @var \Magento\Config\Model\Config\Source\Enabledisable
     */
    protected $_enableDisable;

    protected $_attributeCode = 'certificate';
    protected $_helper;

    /**
     * Activation constructor.
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enableDisable $enableDisable
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \Codecryption\Certificate\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->eavConfig = $eavConfig;
        $this->_helper = $helper;
        $this->_attributeCode = $this->_helper->getAttributValue();
    }

    /**
     * @param string $value
     * @return Magently\Tutorial\Block\Adminhtml\Form\Field\Activation
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Parse to html.
     *
     * @return mixed
     */
    public function _toHtml()
    {
        
        if (!$this->getOptions()) {
            //$attributes = $this->_enableDisable->toOptionArray();
            $attribute = $this->eavConfig->getAttribute('catalog_product', $this->_attributeCode);
             $options = $attribute->getSource()->getAllOptions();

            foreach ($options as $attribute) {
                $this->addOption($attribute['value'], $attribute['label']);
            }
        }

        return parent::_toHtml();
    }
}