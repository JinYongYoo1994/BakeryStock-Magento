<?php
namespace Clyde\Warranty\Controller\Adminhtml\Index;
 
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Clyde\Warranty\Model\ResourceModel\Warranty\CollectionFactory;
 
class TryAgain extends \Magento\Backend\App\Action
{
    
    protected $_filter;
    protected $_warranty;
    protected $_orderManager;
    
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $warranty,
        \Magento\Sales\Model\Order $orderManager
    ) {
        $this->_filter = $filter;
        $this->_warranty = $warranty;
        $this->_orderManager = $orderManager;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $itemIds = $this->getRequest()->getParam('selected');
        if (empty($itemIds) !== false) {
            $collection = $this->_filter->getCollection($this->_warranty->create());
            $itemIds = $collection->getAllIds();
        }

        if (!is_array($itemIds) || empty($itemIds)) {
            $this->messageManager->addError(__('Please select item(s).'));
        } else {
            try {
                 $model = $this->_warranty->create();
                 $totalcount = 0;
                 $model->addFieldToFilter('warranty_id', array('in',$itemIds));
                foreach ($model as $item) {
                    if ($item->getStatus!="Success") {
                        $totalcount = $totalcount + 1;
                        $orderData = $this->_orderManager->loadByIncrementId($item->getOrderId());
                        $finalarray = $this->_helper->setClydedata($order);
                        if (!empty($finalarray['errors'])) { 
                            $item->setData("contract_sale_id", "");
                            $item->setData("status", "Failure:".print_r($finalarray['errors'][0], true));
                        }

                         if (!empty($finalarray['data'])) {
                             $item->setData("contract_sale_id", $finalarray['data']['attributes']['contractSales'][0]['id']);
                             $item->setData("status", "Success");
                         }

                         $item->save();
                    }
                }

                $this->messageManager->addSuccess(
                    __('A total of %1 Clyde warrenty(s) has been updated.', $totalcount)
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        return $this->resultRedirectFactory->create()->setPath('warranty/index/index');
    }
     
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Clyde_Warranty::manage');
    }
}
