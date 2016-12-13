<?php
namespace AppZap\Payment;


use AppZap\Payment\Model\CustomerData;
use AppZap\Payment\Model\OrderInterface;
use AppZap\Payment\Provider\ExternalPaymentProviderInterface;
use AppZap\Payment\Provider\PaymentProviderInterface;
use AppZap\Payment\Session\SessionHandler;
use AppZap\Payment\Session\SessionHandlerInterface;
use AppZap\Tripshop\Service\PaymentProviderNotAllowedException;

abstract class AbstractPaymentService
{

    const RETURN_TYPE_ABORT = 0;
    const RETURN_TYPE_AUTHORIZED = 10;
    const RETURN_TYPE_PAID = 20;
    const RETURN_TYPE_OFFLINE_PAYMENT = 30;
    const RETURN_TYPE_ERROR = 40;

    /**
     * @var string
     */
    protected $encryptionKey;

    /**
     * @param string $encryptionKey
     */
    public function __construct($encryptionKey)
    {
        $this->encryptionKey = $encryptionKey;
    }

    /**
     * @param OrderInterface $order
     * @return array|PaymentProviderInterface[]
     */
    public function getAvailablePaymentProviders(OrderInterface $order)
    {
        $availablePaymentProviderNames = array_filter(
            array_keys(PaymentProviderRegistry::getSupportedPaymentProviders()),
            function($paymentProviderToCheck) use ($order) {
                return $this->paymentProviderIsAvailable($paymentProviderToCheck, $order);
            }
        );
        $paymentFactory = new PaymentFactory();
        $availablePaymentProviders = [];
        foreach ($availablePaymentProviderNames as $availablePaymentProviderName) {
            $availablePaymentProviders[] = $paymentFactory->getPaymentProviderObject($availablePaymentProviderName);
        }
        return $availablePaymentProviders;
    }

    /**
     * @return SessionHandlerInterface
     */
    public function getSessionHandler()
    {
        static $sessionHandler;
        if (!isset($sessionHandler)) {
            $sessionHandler = new SessionHandler();
        }
        return $sessionHandler;
    }

    /**
     * @param OrderInterface $order
     * @param string $returnToken
     * @return int
     * @throws \Exception
     */
    public function evaluateReturnToken(OrderInterface $order, $returnToken)
    {
        foreach ($this->getReturnTypes() as $returnType) {
            if (TokenUtility::evaluateUrlToken($order->getIdentifier(), $order->getRecordToken(), $returnToken, $this->getReturnKey($returnType))) {
                $evaluatedReturnType = $returnType;
            }
        }
        if (!isset($evaluatedReturnType)) {
            $evaluatedReturnType = self::RETURN_TYPE_ERROR;
        }
        return $evaluatedReturnType;
    }

    /**
     * @param OrderInterface $order
     */
    public function execute(OrderInterface $order)
    {
        $this->getPaymentProvider($order)->execute($order->getPayerToken());
    }

    /**
     * @param OrderInterface $order
     * @param string $returnUrl
     * @return string
     */
    public function getPaymentUrl(OrderInterface $order, $returnUrl)
    {
        return $this->getPaymentProvider($order)->getPaymentUrl($order, $returnUrl);
    }

    /**
     * @param OrderInterface $order
     * @return CustomerData|null
     */
    public function getCustomerData(OrderInterface $order)
    {
        $paymentProvider = $this->getPaymentProvider($order);
        if ($paymentProvider instanceof ExternalPaymentProviderInterface) {
            return $paymentProvider->getCustomerData();
        }
        return null;
    }

    /**
     * @param OrderInterface $order
     * @return PaymentProviderInterface
     * @throws PaymentProviderNotAllowedException
     */
    protected function getPaymentProvider(OrderInterface $order)
    {
        if (!$this->paymentProviderIsAvailable($order->getPaymentProviderName(), $order)) {
            throw new PaymentProviderNotAllowedException(
                'Payment provider ' . $order->getPaymentProviderName() . ' is not available for order #' . $order->getIdentifier(), 1456927387);
        }
        static $paymentProviderObjects = [];
        if (!isset($paymentProviderObjects[$order->getPaymentProviderName()])) {
            $paymentProvider = (new PaymentFactory())->getPaymentProviderObject($order->getPaymentProviderName());
            $paymentProvider->setPaymentService($this);
            if ($paymentProvider instanceof ExternalPaymentProviderInterface) {
                $paymentProvider->setAuthenticationConfig($this->getAuthenticationConfig($order->getPaymentProviderName()));
            }
            $paymentProviderObjects[$order->getPaymentProviderName()] = $paymentProvider;
        }
        return $paymentProviderObjects[$order->getPaymentProviderName()];
    }

    /**
     * @param string $paymentProviderName
     * @param OrderInterface $order Unused in this implementation, but parent classes may need it.
     * @return bool
     */
    protected function paymentProviderIsAvailable($paymentProviderName, OrderInterface $order)
    {
        $supportedPaymentProviders = array_keys(PaymentProviderRegistry::getSupportedPaymentProviders());
        return in_array($paymentProviderName, $supportedPaymentProviders);
    }

    /**
     * @param string $type
     * @return string
     */
    public function getReturnKey($type)
    {
        return hash_hmac('sha1', $type, $this->encryptionKey);
    }

    /**
     * @param OrderInterface $order
     * @param string $urlFormat
     * @param int $urlType
     * @return string
     * @throws \Exception
     */
    public function getUrl(OrderInterface $order, $urlFormat, $urlType)
    {
        if (!in_array($urlType, $this->getReturnTypes())) {
            throw new \Exception('Invalid urlType', 1469517895);
        }
        return sprintf(
            $urlFormat,
            TokenUtility::getUrlToken($order->getIdentifier(), $order->getRecordToken(), $this->getReturnKey($urlType))
        );
    }

    /**
     * @return array
     */
    protected function getReturnTypes()
    {
        return [
            self::RETURN_TYPE_ABORT,
            self::RETURN_TYPE_AUTHORIZED,
            self::RETURN_TYPE_OFFLINE_PAYMENT,
            self::RETURN_TYPE_PAID,
        ];
    }

    /**
     * This has to be implemented by the concrete PaymentService to gather configuration for the given payment provider
     *
     * @param string $paymentProviderName
     * @return array
     */
    abstract protected function getAuthenticationConfig($paymentProviderName);

}