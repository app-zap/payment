<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Model\CustomerData;
use AppZap\Payment\Model\OrderInterface;
use PayWithAmazon\Client;

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
        $data = [
            'accesskey' => $this->authenticationConfig['access_key'],
            'amazonpay-button' => null,
            'amount' => $order->getTotalPrice(),
            'currency-code' => $order->getCurrencyCode(),
            'region' => $this->authenticationConfig['region'],
            'sandbox' => $this->authenticationConfig['mode'] === 'sandbox',
            'seller-note' => 'Läuft',
            'sellerid' => $this->authenticationConfig['merchant_id'],
        ];
        $markup = '<div';
        foreach ($data as $key => $value) {
            if ($value === null) {
                $markup .= ' data-' . $key;
            } else {
                $markup .= ' data-' . $key . '=' . $value;
            }
        }
        $markup .= '></div>';
        return $markup;
    }

    /**
     * @return Client
     */
    protected function getAmazonPayClient()
    {
        static $client;
        if (!$client instanceof Client) {
            $client = new Client($this->getClientConfiguration());
        }
        return $client;
    }

    /**
     * @return array
     */
    protected function getClientConfiguration()
    {
        $config = [
            'merchant_id' => $this->authenticationConfig['merchant_id'],
            'access_key' => $this->authenticationConfig['access_key'],
            'secret_key' => $this->authenticationConfig['secret_key'],
            'region' => $this->authenticationConfig['region'],
        ];
        $config['sandbox'] = ($this->authenticationConfig['mode'] === 'sandbox');
        return $config;
    }
}
