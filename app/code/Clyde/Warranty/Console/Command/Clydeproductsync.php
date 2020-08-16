<?php
namespace Clyde\Warranty\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Magento\Framework\Registry;;
use Magento\Framework\ObjectManagerInterface;

class Clydeproductsync extends Command
{
    /**
     * Customer argument
     */
    const PRODUCT_ARGUMENT = '-p';
    const ORDER_ARGUMENT = '-o';
    /**
     * Allow all
     */
    const ALLOW_PRODUCT = 'product';
    const ALLOW_ORDER = 'order';

    /**
     * All customer id
     */
    const ALL_CUSTOMER = 'All';

    /**
     * Order
     *
     * @var Order
     */
    protected $registry;

    protected $objectManager;

    protected $_order;
    protected $_productSync;
    protected $_helper;


    public function __construct(
        Registry $registry,
        ObjectManagerInterface $objectManager
    ) {
        $this->registry = $registry;
        $this->objectManager = $objectManager;
        parent::__construct();
    }

    protected function configure()
    {

        $this->setName('clyde:sync')
            ->setDescription('For product option -p|--product, For order option -o|--order')
            ->setDefinition(
                array(
                new InputOption(
                    self::ALLOW_PRODUCT,
                    self::PRODUCT_ARGUMENT,
                    InputOption::VALUE_NONE,
                    'Sync product with clyde'
                ),
                new InputOption(
                    self::ALLOW_ORDER,
                    self::ORDER_ARGUMENT,
                    InputOption::VALUE_NONE,
                    'Sync order with clyde'
                ),

                )
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $state = $this->objectManager->get('\\Magento\\Framework\\App\\State');
        $state->setAreaCode("adminhtml");

        $this->_order = $this->objectManager->get('\Clyde\Warranty\Model\Order');
        $this->_productSync = $this->objectManager->get('\Clyde\Warranty\Model\ProductSyncToClyde');
        $this->_helper = $this->objectManager->get('\Clyde\Warranty\Helper\Data');
        $allowProduct = $input->getOption(self::ALLOW_PRODUCT);
        $allowOrder = $input->getOption(self::ALLOW_ORDER);
        $helper = $this->getHelper('question');
        if ($allowProduct) {
            //$question = new ConfirmationQuestion('Are you sure you want to sync product with clyde?[y/N]',false);
            //if ($helper->ask($input, $output, $question) || !$input->isInteractive()) {
                //$this->registry->register('isSecureArea',true);
                try {
                    $this->_productSync->getSyncProductCommandAndCron($output, $this->_helper->getProductLimit(), 1);
                } catch (\Exception $e) {
                    throw new \InvalidArgumentException($e->getMessage());
                }

                //$this->registry->unregister('isSecureArea');
                $output->writeln('<info>Products sync successfully!.</info>');
          //  }
        }elseif ($allowOrder) {
            //$question = new ConfirmationQuestion('Are you sure you want to sync order with clyde?[y/N]',false);
            //if ($helper->ask($input, $output, $question) || !$input->isInteractive()) {

                try {
                   $shipmentCount = $this->_order->importOrders($output);
                } catch (\Exception $e) {
                    throw new \InvalidArgumentException($e->getMessage());
                }

                if($shipmentCount > 0){
                    $output->writeln('<info>Order sync successfully!.</info>');
                }elseif($shipmentCount == 0){
                    $output->writeln('<info>Data not found</info>');
                }elseif($shipmentCount == -1){
                    $output->writeln('<info>Order already sync.</info>');
                }

          //  }
        } else {
            throw new \InvalidArgumentException('Argument is missing.');
        }
    }
}
