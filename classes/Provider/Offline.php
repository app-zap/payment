<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Model\OrderInterface;
use AppZap\Payment\AbstractPaymentService;

class Offline extends AbstractPaymentProvider implements PaymentProviderInterface
{

    const PROVIDER_NAME = 'OFFLINE';

    /**
     * When you have configured the payment properly this will give you a URL that you can redirect your visitor to,
     * so that he can pay the desired amount.
     *
     * @param OrderInterface $order
     * @param string $urlFormat
     * @return string
     */
    public function getPaymentUrl(OrderInterface $order, $urlFormat)
    {
        return $this->paymentService->getUrl($order, $urlFormat, AbstractPaymentService::RETURN_TYPE_OFFLINE_PAYMENT);
    }
}
