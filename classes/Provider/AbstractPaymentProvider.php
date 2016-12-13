<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\PaymentService;

abstract class AbstractPaymentProvider implements PaymentProviderInterface
{

    const PROVIDER_NAME = 'OVERWRITE_THIS';

    /**
     * @var array
     */
    protected $authenticationConfig;

    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * @param array $authenticationConfig
     */
    public function setAuthenticationConfig(array $authenticationConfig)
    {
        $this->authenticationConfig = $authenticationConfig;
    }

    /**
     * @param PaymentService $paymentService
     * @return void
     */
    public function setPaymentService(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Indicates wether the payment provider needs javascript access on the checkout page
     *
     * @return bool
     */
    public function isPaymentJavascriptBased()
    {
        return false;
    }

    /**
     * Executes a payment that was previously authorized
     *
     * @param string $paymentToken
     * @return void
     */
    public function execute($paymentToken = null)
    {
    }

    /**
     * Returns the identifier of the payment provider, e.g. MY_PAYMENY_SERVICE
     *
     * @return string
     */
    public function getProviderName()
    {
        $calledClassName = get_called_class();
        return $calledClassName::PROVIDER_NAME;
    }
}
