<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Model\OrderInterface;
use AppZap\Payment\PaymentService;

interface PaymentProviderInterface
{

    /**
     * Returns the identifier of the payment provider, e.g. MY_PAYMENY_SERVICE
     *
     * @return string
     */
    public function getProviderName();

    /**
     * Indicates wether the payment provider needs javascript access on the checkout page
     *
     * @return bool
     */
    public function isPaymentJavascriptBased();

    /**
     * Executes a payment that was previously authorized
     *
     * @param string $paymentToken
     * @return void
     */
    public function execute($paymentToken = null);

    /**
     * Returns the URL the visitor is sent to to either authorize the payment or directly execute it, depending on the
     * configuration.
     *
     * @param OrderInterface $order
     * @param $urlFormat
     * @return string
     */
    public function getPaymentUrl(OrderInterface $order, $urlFormat);

    /**
     * @param PaymentService $paymentService
     * @return void
     */
    public function setPaymentService(PaymentService $paymentService);
}
