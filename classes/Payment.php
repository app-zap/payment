<?php
namespace AppZap\Payment;

use AppZap\Payment\Model\OrderInterface;
use AppZap\Payment\Provider\PaymentProviderInterface;
use AppZap\Payment\Session\SessionHandler;
use AppZap\Payment\Session\SessionHandlerInterface;

/**
 * Class Payment
 *
 * The main class used to configure and prepare the payment.
 *
 * See: ->getPaymentUrl()
 *
 * @package AppZap\Payment
 */
abstract class Payment implements PaymentProviderInterface
{

    const PROVIDER_NAME = '_OVERWRITE_THIS';

    /**
     * @var string
     */
    protected $encryptionKey;

    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var array
     */
    protected $paymentProviderAuthConfig;

    /**
     * @var SessionHandlerInterface
     */
    protected $sessionHandler;

    /**
     * @return string
     */
    public function getProviderName()
    {
        $calledClass = get_called_class();
        return $calledClass::PROVIDER_NAME;
    }

    /**
     * @param string $encryptionKey
     */
    public function setEncryptionKey($encryptionKey)
    {
        $this->encryptionKey = $encryptionKey;
    }

    /**
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * @param array $paymentProviderAuthConfig
     */
    public function setPaymentProviderAuthConfig(array $paymentProviderAuthConfig)
    {
        $this->paymentProviderAuthConfig = $paymentProviderAuthConfig;
    }

    /**
     * @param SessionHandlerInterface $sessionHandler
     */
    public function setSessionHandler(SessionHandlerInterface $sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @return SessionHandlerInterface
     */
    protected function getSessionHandler()
    {
        if (!$this->sessionHandler instanceof SessionHandlerInterface) {
            $this->sessionHandler = new SessionHandler();
        }
        return $this->sessionHandler;
    }

    /**
     * @param string $urlFormat
     * @param int $urlType
     * @return string
     * @throws \Exception
     */
    protected function getUrl($urlFormat, $urlType)
    {
        if (!in_array($urlType, [
            PaymentProviderInterface::RETURN_TYPE_ABORT,
            PaymentProviderInterface::RETURN_TYPE_AUTHORIZED,
            PaymentProviderInterface::RETURN_TYPE_OFFLINE_PAYMENT,
            PaymentProviderInterface::RETURN_TYPE_PAID,
        ])
        ) {
            throw new \Exception('Invalid urlType', 1469517895);
        }
        return sprintf(
            $urlFormat,
            TokenUtility::getUrlToken($this->order->getIdentifier(), $this->order->getRecordToken(), $this->getReturnKey($urlType))
        );
    }

    /**
     * @param string $paymentToken
     * @return void
     */
    public function execute($paymentToken = null)
    {
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getReturnKey($type)
    {
        return hash_hmac('sha1', $type, $this->encryptionKey);
    }

    /**
     * @param OrderInterface $order
     * @param string $returnToken
     * @return int
     * @throws \Exception
     */
    public function evaluateReturnToken(OrderInterface $order, $returnToken)
    {
        $returnTypes = [
            PaymentProviderInterface::RETURN_TYPE_ABORT,
            PaymentProviderInterface::RETURN_TYPE_AUTHORIZED,
            PaymentProviderInterface::RETURN_TYPE_OFFLINE_PAYMENT,
            PaymentProviderInterface::RETURN_TYPE_PAID,
        ];
        foreach ($returnTypes as $returnType) {
            if (TokenUtility::evaluateUrlToken($order->getIdentifier(), $order->getRecordToken(), $returnToken, $this->getReturnKey($returnType))) {
                return $returnType;
            }
        }
        return PaymentProviderInterface::RETURN_TYPE_ERROR;
    }

}
