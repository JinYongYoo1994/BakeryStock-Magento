<?php
namespace Clyde\Warranty\Model\Config\Source;
class Apiurl implements \Magento\Framework\Option\ArrayInterface
{
    const LIVE_UEL = 'https://api.joinclyde.com/';
    const SANDBOX_URL = 'https://sandbox-api.joinclyde.com/';
    public function toOptionArray()
    {
        return array(
            array('value' => self::SANDBOX_URL, 'label' => __('sandbox-api.joinclyde.com (Sandbox)')),
            array('value' => self::LIVE_UEL, 'label' => __('api.joinclyde.com (Production)'))
        );
    }
}