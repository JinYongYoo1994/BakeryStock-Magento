<?php
/**
 * Copyright © 2018 Cardknox Development Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace CardknoxDevelopment\Cardknox\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use CardknoxDevelopment\Cardknox\Gateway\Config\Config;

class TxnIdHandler implements HandlerInterface
{
    const xRefNum = 'xRefNum';
    const xMaskedCardNumber = 'xMaskedCardNumber';
    const xAvsResult = 'xAvsResult';
    const xCvvResult = 'xCvvResult';
    const xCardType = 'xCardType';
    const xToken = 'xToken';
    const xAuthCode = 'xAuthCode';
    const xBatch = 'xBatch';
    const xAuthAmount = 'xAuthAmount';
    const xStatus = 'xStatus';
    const xError = 'xError';
    const xExp = 'xExp';
    const xCvvResultCode = 'xCvvResultCode';
    const xAvsResultCode = 'xAvsResultCode';

    protected $config;
    /**
     * Constructor
     *
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    protected $additionalInformationMapping = [
        self::xMaskedCardNumber,
        self::xAvsResult,
        self::xCvvResult,
        self::xCardType,
        self::xExp,
        self::xBatch,
        self::xRefNum,
        self::xAuthCode,
        self::xAvsResultCode,
        self::xCvvResultCode,
        self::xAuthAmount
    ];


    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];

        $payment = $paymentDO->getPayment();
        /** @var $payment \Magento\Sales\Model\Order\Payment */
        $payment->setTransactionId($response[$this::xRefNum]);
        $payment->setIsTransactionClosed(false);
        if ($payment->getLastTransId() == '') {
            foreach ($this->additionalInformationMapping as $item) {
                if (!isset($response[$item])) {
                    continue;
                }
                $payment->setAdditionalInformation($item, $response[$item]);
            }
        } else {
            if (isset($response[self::xBatch])) {
                //batch only gets added after capturing
                $payment->setAdditionalInformation(self::xBatch, $response[self::xBatch]);
            }
        }
    }
    
    /**
     * Get type of credit card mapped from Cardknox
     *
     * @param string $type
     * @return array
     */
    private function getCreditCardType($type)
    {
//		$replaced = str_replace(' ', '-', strtolower($type));
        $mapper = $this->config->getCctypesMapper();
        return $mapper[$type];
    }

}
