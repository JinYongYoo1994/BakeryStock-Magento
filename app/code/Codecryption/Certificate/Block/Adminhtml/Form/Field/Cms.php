<?php

namespace Codecryption\Certificate\Block\Adminhtml\Form\Field;

class Cms extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Model Enabledisable
     *
     * @var \Magento\Config\Model\Config\Source\Enabledisable
     */
    protected $_blockFactory;


    /**
     * Activation constructor.
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enableDisable $enableDisable
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_blockFactory = $blockFactory;
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
            $cms = $this->_blockFactory->create()->getCollection();
            //echo "<pre>";
            foreach ($cms as $attribute) {
               /* print_r($attribute->getData());*/
                $this->addOption($attribute->getIdentifier(), $attribute->getTitle());
            }
            //echo "</pre>"; exit;
        }

        return parent::_toHtml();
    }
}