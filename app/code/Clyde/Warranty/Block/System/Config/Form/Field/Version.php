<?php
namespace Clyde\Warranty\Block\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    const EXTENSION_URL = 'https://www.clyde.com';

    protected $_helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Clyde\Warranty\Helper\Data $helper
    ) {
        $this->_helper = $helper;
        parent::__construct($context);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $extensionVersion   = $this->_helper->getExtensionVersion();
        $versionLabel       = sprintf('<a href="%s" title="Cart Discount" target="_blank">%s</a>', self::EXTENSION_URL, $extensionVersion);
        $element->setValue($versionLabel);

        return $element->getValue();
    }
}