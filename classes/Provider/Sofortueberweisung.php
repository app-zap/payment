<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Model\CustomerData;
use AppZap\Payment\Model\OrderInterface;
use AppZap\Payment\PaymentService;

class Sofortueberweisung extends AbstractPaymentProvider implements PaymentProviderInterface, ExternalPaymentProviderInterface
{

    const PROVIDER_NAME = 'SOFORTUEBERWEISUNG';

    /**
     * When you have configured the payment properly this will give you a URL that you can redirect your visitor to,
     * so that he can pay the desired amount.
     *
     * @param OrderInterface $order
     * @param string $urlFormat
     * @return string
     * @throws \Exception
     */
    public function getPaymentUrl(OrderInterface $order, $urlFormat)
    {
        if (
            !is_array($this->authenticationConfig) ||
            !isset($this->authenticationConfig['configkey'])
        ) {
            throw new \Exception('Auth Config for Provider ' . self::PROVIDER_NAME . ' is not set.', 1394785987);
        }

        $totalPrice = $order->getTotalPrice();
        if ($totalPrice === (float)0) {
            throw new \Exception('Total price is 0. Provider ' . self::PROVIDER_NAME . ' does not support free payments.', 1394786580);
        }

        $sofort = new \Sofortueberweisung($this->authenticationConfig['configkey']);
        $sofort->setAmount($totalPrice);
        $sofort->setCurrencyCode('EUR');
        $sofort->setSenderCountryCode('DE');
        $sofort->setReason($order->getReason());
        $sofort->setSuccessUrl($this->paymentService->getUrl($order, $urlFormat, PaymentService::RETURN_TYPE_PAID));
        $sofort->setAbortUrl($this->paymentService->getUrl($order, $urlFormat, PaymentService::RETURN_TYPE_ABORT));
        $sofort->sendRequest();
        $url = $sofort->getPaymentUrl();
        return $url;
    }

    /**
     * @return CustomerData|null
     */
    public function getCustomerData()
    {
        // TODO: Implement getCustomerData() method.
        return null;
    }
}
