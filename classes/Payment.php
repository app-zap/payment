<?php

namespace AppZap\Payment;
use AppZap\Payment\Provider\Offline;
use AppZap\Payment\Provider\Paypal;
use AppZap\Payment\Provider\Sofortueberweisung;

/**
 * Class Payment
 *
 * The main class used to configure and prepare the payment.
 *
 * See: ->getPaymentUrl()
 *
 * @package AppZap\Payment
 */
abstract class Payment
{

    /**
     * @var string
     */
    protected $abortKey;

    /**
     * @var bool
     */
    protected $debugTokenUrls = false;

    /**
     * @var \AppZap\Payment\Model\Order
     */
    protected $order;

    /**
     * @var array
     */
    protected $paymentProviderAuthConfig;

    /**
     * @var string
     */
    protected $successKey;

    /**
     * @param $paymentProvider
     * @return \AppZap\Payment\Payment
     * @throws \InvalidArgumentException
     */
    public static function getInstance($paymentProvider)
    {

        $supportedPaymentProviders = array(
            Paypal::PROVIDER_NAME => '\AppZap\Payment\Provider\Paypal',
            Sofortueberweisung::PROVIDER_NAME => '\AppZap\Payment\Provider\Sofortueberweisung',
            Offline::PROVIDER_NAME => '\AppZap\Payment\Provider\Offline',
        );

        if (in_array($paymentProvider, array_keys($supportedPaymentProviders))) {
            return new $supportedPaymentProviders[$paymentProvider];
        } else {
            throw new \InvalidArgumentException('Payment provider ' . htmlentities($paymentProvider) . ' is not supported.');
        }
    }

    /**
     * @param string $abortKey
     */
    public function setAbortKey($abortKey)
    {
        $this->abortKey = $abortKey;
    }

    /**
     * @return string
     */
    public function getAbortKey()
    {
        return $this->abortKey;
    }

    /**
     * @param boolean $debugTokenUrls
     */
    public function setDebugTokenUrls($debugTokenUrls)
    {
        $this->debugTokenUrls = $debugTokenUrls;
    }

    /**
     * @return boolean
     */
    public function getDebugTokenUrls()
    {
        return $this->debugTokenUrls;
    }

    /**
     * @param \AppZap\Payment\Model\Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return \AppZap\Payment\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param array $paymentProviderAuthConfig
     */
    public function setPaymentProviderAuthConfig($paymentProviderAuthConfig)
    {
        $this->paymentProviderAuthConfig = $paymentProviderAuthConfig;
    }

    /**
     * @return array
     */
    public function getPaymentProviderAuthConfig()
    {
        return $this->paymentProviderAuthConfig;
    }

    /**
     * @param string $successKey
     */
    public function setSuccessKey($successKey)
    {
        $this->successKey = $successKey;
    }

    /**
     * @return string
     */
    public function getSuccessKey()
    {
        return $this->successKey;
    }

    /**
     * When you have configured the payment properly this will give you a URL that you can redirect your visitor to,
     * so that he can pay the desired amount.
     *
     * @param string $urlFormat
     * @return string
     */
    abstract public function getPaymentUrl($urlFormat);

    /**
     * @param string $urlFormat
     * @return string
     */
    public function getAbortUrl($urlFormat)
    {
        return sprintf(
            $urlFormat,
            TokenUtility::getUrlToken($this->order->getIdentifier(), $this->order->getRecordToken(), $this->getAbortKey())
        );
    }

    /**
     * @param string $urlFormat
     * @return string
     */
    public function getSuccessUrl($urlFormat)
    {
        return sprintf(
            $urlFormat,
            TokenUtility::getUrlToken($this->order->getIdentifier(), $this->order->getRecordToken(), $this->getSuccessKey())
        );
    }

    public function execute()
    {
    }

}

?>
