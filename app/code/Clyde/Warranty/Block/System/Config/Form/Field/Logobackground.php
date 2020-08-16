<?php
namespace Clyde\Warranty\Block\System\Config\Form\Field;

use Magento\Framework\Registry;

class Logobackground extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_coreRegistry;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }
    
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $base = $this->getBaseUrl();
        $html = $element->getElementHtml();
        $jsPath = $this->getViewFileUrl('Clyde_Warranty/js/jscolor.js');
        $imagePath = $this->getViewFileUrl('Clyde_Warranty/images/color.png');
        if(!$this->_coreRegistry->registry('colorpicker_loaded')) {
            $html .= '<script type="text/javascript" src="'. $jsPath .'"></script><style type="text/css">input.jscolor { background-image: url('.$imagePath.') !important; background-position: calc(100% - 8px) center; background-repeat: no-repeat; padding-right: 44px !important; } input.jscolor.disabled,input.jscolor[disabled] { pointer-events: none; }</style>';
            $this->_coreRegistry->registry('colorpicker_loaded', 1);
        }

        $html .= '<script type="text/javascript">
                var el = document.getElementById("'. $element->getHtmlId() .'");
                el.className = el.className + " jscolor";
            </script>';
        return $html;
    }
}