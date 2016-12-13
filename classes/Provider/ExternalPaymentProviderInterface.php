<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Model\CustomerData;

interface ExternalPaymentProviderInterface
{

    /**
     * Set the configuration to authenticate at the payment service.
     * The concrete configuration varies for each provider.
     * @param array $authenticationConfig
     * @return void
     */
    public function setAuthenticationConfig(array $authenticationConfig);

    /**
     * @return CustomerData
     */
    public function getCustomerData();
}
