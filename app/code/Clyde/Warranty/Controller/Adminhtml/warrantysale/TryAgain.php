<?php
namespace Clyde\Warranty\Controller\Adminhtml\warrantysale;

use Magento\Backend\App\Action;
/**
 * Class MassDelete
 */
class TryAgain extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
     protected $_orderManager;
     public function __construct(
         Action\Context $context,
         \Magento\Sales\Model\Order $orderManager
     ) {
        $this->_orderManager = $orderManager;
        parent::__construct($context);
     }
    public function execute()
    {
        $itemIds = $this->getRequest()->getParam('warrantysale');
        if (!is_array($itemIds) || empty($itemIds)) {
            $this->messageManager->addError(__('Please select item(s).'));
        } else {
            try {
                $totalcount = 0;
                $helper = $this->_objectManager->create('Clyde\Warranty\Helper\Data');
                foreach ($itemIds as $itemId) {
                    $post = $this->_objectManager->get('Clyde\Warranty\Model\Warrantysale')->load($itemId);
                    if($post['status']!="Success"){
                        $totalcount = $totalcount + 1;
                        $orderData = $this->_orderManager->loadByIncrementId($post->getOrderId());
                        $finalarray = $helper->setClydedata($orderData);
                        if(!empty($finalarray['errors'])){ 
                            $post->setData("contract_sale_id", "");
                            $post->setData("status", "Failure:".print_r($finalarray['errors'][0], true));
                        }

                         if(!empty($finalarray['data'])){
                             $post->setData("contract_sale_id", $finalarray['data']['attributes']['contractSales'][0]['id']);
                             $post->setData("status", "Success");
                         }

                         $post->save();
                    }
                }

                $this->messageManager->addSuccess(
                    __('A total of %1 clyde request(s) has been updated.', $totalcount)
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        return $this->resultRedirectFactory->create()->setPath('warranty/*/index');
    }
}