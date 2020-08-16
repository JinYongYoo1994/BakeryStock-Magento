<?php
namespace Clyde\Warranty\Model\Warranty;

class Staticblock
{
    protected $mode; 

    protected $Staticblocks; 

    public function __construct(
        \Magento\Catalog\Model\Category\Attribute\Source\Mode $mode,
        \Magento\Catalog\Model\Category\Attribute\Source\Page $Staticblocks
    ) {
        $this->mode = $mode;
        $this->Staticblocks = $Staticblocks;
    }

    public function toOptionArray()
    {
        return $this->mode->getAllOptions();
    }

    public function getAllOptions()
    {
        return $this->Staticblocks->getAllOptions();
    }
}
