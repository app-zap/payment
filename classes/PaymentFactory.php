<?php
namespace AppZap\Payment;

use AppZap\Payment\Provider\PaymentProviderInterface;

class PaymentFactory
{

    /**
     * @param string $paymentProviderName
     * @return PaymentProviderInterface
     */
    public function getPaymentProviderObject($paymentProviderName)
    {
        $supportedPaymentProviders = PaymentProviderRegistry::getSupportedPaymentProviders();
        if (array_key_exists($paymentProviderName, $supportedPaymentProviders)) {
            return new $supportedPaymentProviders[$paymentProviderName];
        } else {
            throw new \InvalidArgumentException('Payment provider ' . htmlentities($paymentProviderName) . ' is not supported.', 1447533889);
        }
    }

}
