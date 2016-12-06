<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Payment;
use PayWithAmazon\Client;

class AmazonPay extends Payment implements PaymentProviderInterface
{

    const PROVIDER_NAME = 'AMAZON_PAY';

    /**
     * When you have configured the payment properly this will give you a URL that you can redirect your visitor to,
     * so that he can pay the desired amount.
     *
     * @param $urlFormat
     * @return string
     * @throws \Exception
     */
    public function getPaymentUrl($urlFormat)
    {
        if (
            !is_array($this->paymentProviderAuthConfig[self::PROVIDER_NAME]) ||
            !isset($this->paymentProviderAuthConfig[self::PROVIDER_NAME]['merchant_id']) ||
            !isset($this->paymentProviderAuthConfig[self::PROVIDER_NAME]['access_key']) ||
            !isset($this->paymentProviderAuthConfig[self::PROVIDER_NAME]['secret_key']) ||
            !isset($this->paymentProviderAuthConfig[self::PROVIDER_NAME]['region'])
        ) {
            throw new \Exception('Auth Config for Provider ' . self::PROVIDER_NAME . ' is not set.', 1394795187);
        }

        if (!empty($this->order->getPayerToken())) {
            // The payment was previously authorized and can now be completed directly
            return $this->getUrl($urlFormat, PaymentProviderInterface::RETURN_TYPE_PAID);
        }

        // TODO: Does amazon support free payments?
        $totalPrice = $this->order->getTotalPrice();
        if ($totalPrice == 0) {
            throw new \Exception('Total price is 0. Provider ' . self::PROVIDER_NAME . ' does not support free payments.', 1394795478);
        }

        $config = $this->paymentProviderAuthConfig[self::PROVIDER_NAME];
        $config['sandbox'] = true;

        $client = new Client($config);
        $response = $client->charge([]);
    }

    /**
     * @return bool
     */
    public function isExternalProvider()
    {
        return true;
    }
}
