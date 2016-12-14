<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Model\OrderInterface;

interface JavascriptBasedPaymentProviderInterface
{

    /**
     * @param OrderInterface $order
     * @return string
     */
    public function getButtonMarkup(OrderInterface $order);
}
