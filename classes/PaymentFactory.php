<?php
namespace AppZap\Payment;

use AppZap\Payment\Provider\Offline;
use AppZap\Payment\Provider\PaymentProviderInterface;
use AppZap\Payment\Provider\Paypal;
use AppZap\Payment\Provider\Sofortueberweisung;

class PaymentFactory
{

    /**
     * @param string $paymentProviderName
     * @return PaymentProviderInterface
     */
    public function getPaymentProviderObject($paymentProviderName)
    {
        // todo: move the supported payment providers to separate composer packages and offer an API to register themselves
        $supportedPaymentProviders = array(
            Paypal::PROVIDER_NAME => Paypal::class,
            Sofortueberweisung::PROVIDER_NAME => Sofortueberweisung::class,
            Offline::PROVIDER_NAME => Offline::class,
        );

        if (array_key_exists($paymentProviderName, $supportedPaymentProviders)) {
            return new $supportedPaymentProviders[$paymentProviderName];
        } else {
            throw new \InvalidArgumentException('Payment provider ' . htmlentities($paymentProviderName) . ' is not supported.', 1447533889);
        }
    }

}
