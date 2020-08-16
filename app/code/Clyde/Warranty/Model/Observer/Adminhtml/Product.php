<?php 
namespace Clyde\Warranty\Model\Observer\Adminhtml;

use Magento\Framework\Event\Observer as EventObserver;

use Magento\Framework\Event\ObserverInterface;

class Product implements ObserverInterface
{
    protected $resourceConnection;
    
    protected $eavAttributeFactory;
    
    protected $request;
    
    protected $_resources;
    
    protected $productFactory;

    protected $_helper;
    
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Eav\Model\Entity\AttributeFactory $eavAttributeFactory,
        \Clyde\Warranty\Helper\Data $helper,
        \Clyde\Warranty\Model\Product $productFactory
    ) {
       $this->request = $request;
       $this->resourceConnection = $resourceConnection;
       $this->eavAttributeFactory = $eavAttributeFactory;
       $this->productFactory = $productFactory;
       $this->_helper = $helper;
    }
   
    public function execute(EventObserver $observer)
    {
        if($this->_helper->getEnableModule() == 1){
            $product = $observer->getEvent()->getProduct();
            $product_id = $product->getId();
            $selectedWarranties = $this->request->getPost();
            if (isset($selectedWarranties['selected_warranties'])) {
                $productModel = $this->productFactory;
                $selected = json_decode($selectedWarranties['selected_warranties'], true);
                $warranties = array_keys($selected);
                $productModel->setProductWarranty($product_id, $warranties);
            } 
        }
        
    }
}