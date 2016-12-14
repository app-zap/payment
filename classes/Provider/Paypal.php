<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Model\CustomerData;
use AppZap\Payment\Model\OrderInterface;
use AppZap\Payment\AbstractPaymentService;
use PayPal\Api\Address;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class Paypal extends AbstractPaymentProvider implements PaymentProviderInterface, ExternalPaymentProviderInterface
{

    const MODE_LIVE = 'live';
    const MODE_SANDBOX = 'sandbox';

    const PROVIDER_NAME = 'PAYPAL';

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
            !isset($this->authenticationConfig['clientid']) ||
            !isset($this->authenticationConfig['secret'])
        ) {
            throw new \Exception('Auth Config for Provider ' . self::PROVIDER_NAME . ' is not set.', 1394795187);
        }

        if (!empty($order->getPayerToken())) {
            // The payment was previously authorized and can now be completed directly
            return $this->paymentService->getUrl($order, $urlFormat, AbstractPaymentService::RETURN_TYPE_PAID);
        }

        $totalPrice = $order->getTotalPrice();
        if ($totalPrice == 0) {
            throw new \Exception('Total price is 0. Provider ' . self::PROVIDER_NAME . ' does not support free payments.', 1394795478);
        }

        if (!in_array($this->authenticationConfig['mode'], [self::MODE_SANDBOX, self::MODE_LIVE])) {
            throw new \Exception('No valid API mode given.', 1399294820);
        }

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $amount = new Amount();
        $amount->setCurrency($order->getCurrencyCode());
        $amount->setTotal((float)$order->getTotalPrice());
        $transaction = new Transaction();
        $transaction->setDescription($order->getReason());
        $transaction->setItemList($this->getItemList($order));
        $transaction->setAmount($amount);
        $redirectUrls = new RedirectUrls();
        if ((bool)$this->authenticationConfig['commitPayment']) {
            $successType = AbstractPaymentService::RETURN_TYPE_PAID;
        } else {
            $successType = AbstractPaymentService::RETURN_TYPE_AUTHORIZED;
        }
        $redirectUrls->setReturnUrl($this->paymentService->getUrl($order, $urlFormat, $successType));
        $redirectUrls->setCancelUrl($this->paymentService->getUrl($order, $urlFormat, AbstractPaymentService::RETURN_TYPE_ABORT));
        $payment = new \PayPal\Api\Payment();
        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions([$transaction]);
        $payment->create($this->getApiContext());
        $this->paymentService->getSessionHandler()->store('paymentId', $payment->getId());
        $paymentUrl = '';
        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() === 'approval_url') {
                $paymentUrl = $link->getHref();
            }
        }
        if ((bool)$this->authenticationConfig['commitPayment']) {
            $paymentUrl .= '&useraction=commit';
        }
        return $paymentUrl;
    }

    /**
     * @param string $payerId
     * @throws \Exception
     */
    public function execute($payerId)
    {
        if (!empty($payerId)) {
            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);
            $returnedState = $this->getPayment()->execute($execution, $this->getApiContext());
            if ($returnedState->getIntent() !== 'sale') {
                throw new \Exception('Paypal Payment execution failed', 1399884990);
            }
        } else {
            throw new \Exception('Paypal Payment execution failed: No PayerID given.', 1399987976);
        }
    }

    /**
     * @return CustomerData
     */
    public function getCustomerData()
    {
        $payerInfo = $this->getPayment()->payer->getPayerInfo();
        $customerData = new CustomerData();
        $address = $payerInfo->getBillingAddress();
        if (!$address instanceof Address) {
            $address = $payerInfo->getShippingAddress();
        }
        if ($address instanceof Address) {
            $customerData->setAddressAdditionalInfo($address->getLine2());
            $customerData->setAddressCity($address->getCity());
            $customerData->setAddressPostalCode($address->getPostalCode());
            $customerData->setAddressStreet($address->getLine1());
        }
        $customerData->setEmail($payerInfo->getEmail());
        $customerData->setFirstName($payerInfo->getFirstName());
        $customerData->setLastName($payerInfo->getLastName());
        $customerData->setMiddleName($payerInfo->getMiddleName());
        $customerData->setNameSuffix($payerInfo->getSuffix());
        $customerData->setPayerToken($payerInfo->getPayerId());
        $customerData->setPhone($payerInfo->getPhone());
        $customerData->setPhoneType($payerInfo->getPhoneType());
        $customerData->setSalutation($payerInfo->getSalutation());
        return $customerData;
    }

    /**
     * @param OrderInterface $order
     * @return ItemList
     */
    protected function getItemList(OrderInterface $order)
    {
        $items = array();
        foreach ($order->getOrderItems() as $orderItem) {
            $item = new Item();
            $item->setName($orderItem->getTitle());
            $item->setQuantity($orderItem->getQuantity());
            $item->setSku($orderItem->getArticleNumber());
            $item->setPrice((float)$orderItem->getPrice() / $orderItem->getQuantity());
            $item->setCurrency('EUR');
            $items[] = $item;
        }
        $itemList = new ItemList();
        $itemList->setItems($items);
        return $itemList;
    }

    /**
     * @return \PayPal\Api\Payment
     */
    protected function getPayment()
    {
        static $payment;
        if (!$payment instanceof \PayPal\Api\Payment) {
            $payment = \PayPal\Api\Payment::get($this->paymentService->getSessionHandler()->get('paymentId'), $this->getApiContext());
        }
        return $payment;
    }

    /**
     * @return ApiContext
     */
    protected function getApiContext()
    {
        static $apiContext;
        if (!$apiContext instanceof ApiContext) {
            $apiContext = new ApiContext(new OAuthTokenCredential(
                $this->authenticationConfig['clientid'],
                $this->authenticationConfig['secret']
            ));
            $apiContext->setConfig(array(
                'mode' => $this->authenticationConfig['mode'],
                'http.ConnectionTimeOut' => 30,
                'log.LogEnabled' => false,
            ));
        }
        return $apiContext;
    }
}
