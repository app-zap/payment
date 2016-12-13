<?php
namespace AppZap\Payment;

class PaymentProviderRegistry
{

    /**
     * @var array
     */
    protected static $supportedPaymentProviders = [];

    /**
     * @param string $providerName
     * @param string $providerClassName
     */
    public static function addSupportedPaymentProvider($providerName, $providerClassName)
    {
        self::$supportedPaymentProviders[$providerName] = $providerClassName;
    }

    /**
     * @return array
     */
    public static function getSupportedPaymentProviders()
    {
        return self::$supportedPaymentProviders;
    }
}
