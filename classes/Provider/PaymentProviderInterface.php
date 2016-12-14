<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Model\OrderInterface;
use AppZap\Payment\AbstractPaymentService;

interface PaymentProviderInterface
{

    /**
     * Returns the identifier of the payment provider, e.g. MY_PAYMENY_SERVICE
     *
     * @return string
     */
    public function getProviderName();

    /**
     * Executes a payment that was previously authorized
     *
     * @param string $paymentToken
     * @return void
     */
    public function execute($paymentToken);

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
     * @param AbstractPaymentService $paymentService
     * @return void
     */
    public function setPaymentService(AbstractPaymentService $paymentService);
}
