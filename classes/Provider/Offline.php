<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Payment;

class Offline extends Payment implements PaymentProviderInterface
{

    const PROVIDER_NAME = 'OFFLINE';

    /**
     * When you have configured the payment properly this will give you a URL that you can redirect your visitor to,
     * so that he can pay the desired amount.
     *
     * @param string $urlFormat
     * @return string
     */
    public function getPaymentUrl($urlFormat)
    {
        return $this->getOfflinePaymentUrl($urlFormat);
    }
}
