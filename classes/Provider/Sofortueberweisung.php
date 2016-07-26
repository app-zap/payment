<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Payment;

class Sofortueberweisung extends Payment implements PaymentProviderInterface
{

    const PROVIDER_NAME = 'SOFORTUEBERWEISUNG';

    /**
     * When you have configured the payment properly this will give you a URL that you can redirect your visitor to,
     * so that he can pay the desired amount.
     *
     * @param string $urlFormat
     * @return string
     * @throws \Exception
     */
    public function getPaymentUrl($urlFormat)
    {
        if (
            !is_array($this->paymentProviderAuthConfig[self::PROVIDER_NAME]) ||
            !isset($this->paymentProviderAuthConfig[self::PROVIDER_NAME]['configkey'])
        ) {
            throw new \Exception('Auth Config for Provider ' . self::PROVIDER_NAME . ' is not set.', 1394785987);
        }

        $totalPrice = $this->order->getTotalPrice();
        if ($totalPrice === (float)0) {
            throw new \Exception('Total price is 0. Provider ' . self::PROVIDER_NAME . ' does not support free payments.', 1394786580);
        }

        $sofort = new \Sofortueberweisung($this->paymentProviderAuthConfig[self::PROVIDER_NAME]['configkey']);
        $sofort->setAmount($totalPrice);
        $sofort->setCurrencyCode('EUR');
        $sofort->setSenderCountryCode('DE');
        $sofort->setReason($this->order->getReason());
        $sofort->setSuccessUrl($this->getUrl($urlFormat, PaymentProviderInterface::RETURN_TYPE_PAID));
        $sofort->setAbortUrl($this->getUrl($urlFormat, PaymentProviderInterface::RETURN_TYPE_ABORT));
        $sofort->sendRequest();
        $url = $sofort->getPaymentUrl();
        return $url;
    }

    /**
     * @return bool
     */
    public function isExternalProvider()
    {
        return true;
    }
}
