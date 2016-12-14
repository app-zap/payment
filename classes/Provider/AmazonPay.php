<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Model\CustomerData;
use AppZap\Payment\Model\OrderInterface;

class AmazonPay extends AbstractPaymentProvider implements
    PaymentProviderInterface,
    ExternalPaymentProviderInterface,
    JavascriptBasedPaymentProviderInterface
{

    const PROVIDER_NAME = 'AMAZON_PAY';

    /**
     * Returns the URL the visitor is sent to to either authorize the payment or directly execute it, depending on the
     * configuration.
     *
     * @param OrderInterface $order
     * @param $urlFormat
     * @return string
     */
    public function getPaymentUrl(OrderInterface $order, $urlFormat)
    {
        // TODO
    }

    /**
     * @return CustomerData
     */
    public function getCustomerData()
    {
        // TODO
    }

    /**
     * @param string $paymentToken
     */
    public function execute($paymentToken)
    {
        // TODO
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    public function getButtonMarkup(OrderInterface $order)
    {
        // TODO: Implement getButtonMarkup() method.
    }
}
