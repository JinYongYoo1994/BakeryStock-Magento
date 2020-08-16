<?php
namespace Clyde\Warranty\Model;
 
class Customerwarranty extends \Magento\Framework\Model\AbstractModel
{
    protected $_helper;
   
    protected $logger;
   
    protected $_storeManager;
   
    protected $_countryFactory;
   
    protected $messageManager;
   
    const CACHE_TAG = 'clyde_customerwarranty';
   
    protected $_cacheTag = 'clyde_customer_warranty';
   
    protected $_eventPrefix = 'clyde_customerwarranty';
   
    protected $_transportBuilder = '';
   
    protected function _construct()
    {
        $this->_init('Clyde\Warranty\Model\ResourceModel\Customerwarranty');
    }
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Clyde\Warranty\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Directory\Model\CountryFactory $CountryFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        
        array $data = array()
    ) {
        $this->_helper = $helper;
        $this->logger = $context->getLogger();
        $this->_storeManager = $store;
        $this->_countryFactory = $CountryFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
        parent::__construct($context, $registry);
    }
}
