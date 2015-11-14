<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Model\OrderInterface;
use AppZap\Payment\Session\SessionHandlerInterface;

interface PaymentProviderInterface
{

    const RETURN_TYPE_ABORT = 0;
    const RETURN_TYPE_PAID = 1;
    const RETURN_TYPE_OFFLINE_PAYMENT = 2;
    const RETURN_TYPE_ERROR = 3;

    /**
     * @return void
     */
    public function execute();

    /**
     * When you have configured the payment properly this will give you a URL that you can redirect your visitor to,
     * so that he can pay the desired amount.
     *
     * @param $urlFormat
     * @return string
     */
    public function getPaymentUrl($urlFormat);

    /**
     * @param string $key
     * @return void
     */
    public function setEncryptionKey($key);

    /**
     * @param OrderInterface $order
     * @return void
     */
    public function setOrder(OrderInterface $order);

    /**
     * @param array $paymentProviderAuthConfig
     * @return void
     */
    public function setPaymentProviderAuthConfig(array $paymentProviderAuthConfig);

    /**
     * @param SessionHandlerInterface $sessionHandler
     * @return void
     */
    public function setSessionHandler(SessionHandlerInterface $sessionHandler);

    /**
     * @param OrderInterface $order
     * @param string $returnToken
     * @return int
     */
    public function evaluateReturnToken(OrderInterface $order, $returnToken);

}
