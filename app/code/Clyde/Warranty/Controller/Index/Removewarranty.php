<?php
namespace Clyde\Warranty\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Checkout\Model\Cart as CustomerCart;

class Removewarranty extends \Magento\Framework\App\Action\Action
{
    protected $_cacheTypeList;
    
    protected $_cacheState;
    
    protected $_cacheFrontendPool;
    
    protected $resultPageFactory;
    
    public $_helper;
    
    protected $_warrantyFactory;

    protected $formKey;

    protected $_customerCart;
 
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Clyde\Warranty\Helper\Data $helper,
        \Clyde\Warranty\Model\Warranty $warrantyFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        CustomerCart $customerCart
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_helper = $helper;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->_warrantyFactory = $warrantyFactory;
        $this->formKey = $formKey;
        $this->_customerCart = $customerCart;
    }
    
    public function execute()
    {
        $data = (array)$this->getRequest()->getPost();
        if (isset($data['quote']) && isset($data['itemid']) && isset($data['warranty'])) {
            $item = $this->_customerCart->getQuote()->getItemById($data['itemid']);

            try {
                $this->addUpdateItemOptions($data, $item);
                $result['success'] = true ;
                $result['html'] = '';
                $result['message'] = __('Warranty removed successfully!');
            } catch (\Exception $e) {
                $result['error'] = true ;
                $result['message'] =   __($e->getMessage());
            }
        } else {
           $result['error'] = true ;
           $result['message'] =   __('Data not valide');;
        }
   
        /* Item Cart */ 
        $resultPage = $this->resultPageFactory ->create();
        $resultPage->addHandle(array('default', 'checkout_cart_index'));
        $block = $resultPage->getLayout()->getBlock('checkout.cart');
        $data = $block->getBlockHtml('checkout.cart.form'); 
        $result['cart'] =   $data;
        /* end */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }
    
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function addUpdateItemOptions($data ,$item)
    {
        $param[$data['itemid']] = array('qty' => $item->getQty() );
        $item = $this->_customerCart->updateItems($param)->save();
        //$this->_customerCart->save();        
    }

    public function getItemOrgPrice($item)
    {
        if ($item->getWarrantyInfo() != '') {
            $info = $this->_helper->decryptString($item->getWarrantyInfo());
            if (isset($info['item_price']) && $info['item_price'] != '') {
                return $info['item_price'];
            }
        }

        return $item->getProduct()->getPrice();
    }
    
    public function getWarrantyDetail($warranty_id , $item)
    {
        $warranty = $this->_warrantyFactory->getWarrantryRowBySku($item->getProductId(), $item->getProduct()->getSku(), $warranty_id);   
        return $warranty;
    }
    
    public function checkOption($additionalOption)
    { 
         if (empty($additionalOption) != true) {
            foreach ($additionalOption as $key=>$value) {
                if (strtolower($value['label']) === "warranty") {
                    unset($additionalOption[$key]);
                }
            }
         }
 
        return $additionalOption;
    }
}
