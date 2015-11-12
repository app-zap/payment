<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Payment;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class Paypal extends Payment
{

    const MODE_LIVE = 'live';
    const MODE_SANDBOX = 'sandbox';

    const PROVIDER_NAME = 'PAYPAL';

    /**
     * When you have configured the payment properly this will give you a URL that you can redirect your visitor to,
     * so that he can pay the desired amount.
     *
     * @param string $urlFormat
     * @return string
     * @throws \Exception
     */
    public function getPaymentUrl($urlFormat)
    {
        if (
            !is_array($this->paymentProviderAuthConfig[self::PROVIDER_NAME]) ||
            !isset($this->paymentProviderAuthConfig[self::PROVIDER_NAME]['clientid']) ||
            !isset($this->paymentProviderAuthConfig[self::PROVIDER_NAME]['secret'])
        ) {
            throw new \Exception('Auth Config for Provider ' . self::PROVIDER_NAME . ' is not set.', 1394795187);
        }

        $totalPrice = $this->order->getTotalPrice();
        if ($totalPrice == 0) {
            throw new \Exception('Total price is 0. Provider ' . self::PROVIDER_NAME . ' does not support free payments.', 1394795478);
        }

        if (!in_array($this->paymentProviderAuthConfig[self::PROVIDER_NAME]['mode'], array(self::MODE_SANDBOX, self::MODE_LIVE))) {
            throw new \Exception('No valid API mode given.', 1399294820);
        }

        $apiContext = $this->createApiContext();
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $amount = new Amount();
        $amount->setCurrency("EUR");
        $amount->setTotal(number_format($this->order->getTotalPrice(), 2));
        $transaction = new Transaction();
        $transaction->setDescription($this->order->getReason());
        $transaction->setItemList($this->getItemList());
        $transaction->setAmount($amount);
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->getSuccessUrl($urlFormat));
        $redirectUrls->setCancelUrl($this->getAbortUrl($urlFormat));
        $payment = new \PayPal\Api\Payment();
        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions([$transaction]);
        $payment->create($apiContext);
        $_SESSION['app-zap/payment/payment_id'] = $payment->getId();
        $paymentUrl = '';
        foreach ($payment->getLinks() as $link) {
            /** @var \PayPal\Api\Links $link */
            if ($link->getRel() === 'approval_url') {
                $paymentUrl = $link->getHref();
            }
        }
        return $paymentUrl . '&useraction=commit';
    }

    public function execute()
    {
        $querystring = $_SERVER['QUERY_STRING'];
        $params = array();
        parse_str($querystring, $params);
        if (isset($params['PayerID'])) {
            $apiContext = $this->createApiContext();
            $paymentId = $_SESSION['app-zap/payment/payment_id'];
            $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);
            $execution = new PaymentExecution();
            $execution->setPayerId($params['PayerID']);
            $returnedState = $payment->execute($execution, $apiContext);
            if ($returnedState->getIntent() !== 'sale') {
                throw new \Exception('Paypal Payment execution failed', 1399884990);
            }
        } else {
            throw new \Exception('Paypal Payment execution failed: No PayerID given.', 1399987976);
        }
    }

    /**
     * @return ItemList
     */
    protected function getItemList()
    {
        $items = array();
        foreach ($this->order->getOrderItems() as $orderItem) {
            /** @var \AppZap\Payment\Model\OrderItem $orderItem */
            $item = new Item();
            $item->setName($orderItem->getTitle());
            $item->setQuantity($orderItem->getQuantity());
            $item->setSku($orderItem->getArticleNumber());
            $item->setPrice(number_format($orderItem->getPrice() / $orderItem->getQuantity(), 2));
            $item->setCurrency('EUR');
            $items[] = $item;
        }
        $itemList = new ItemList();
        $itemList->setItems($items);
        return $itemList;
    }

    /**
     * @return ApiContext
     */
    protected function createApiContext()
    {
        $apiContext = new ApiContext(new OAuthTokenCredential(
            $this->paymentProviderAuthConfig[self::PROVIDER_NAME]['clientid'],
            $this->paymentProviderAuthConfig[self::PROVIDER_NAME]['secret']
        ));
        $apiContext->setConfig(array(
            'mode' => $this->paymentProviderAuthConfig[self::PROVIDER_NAME]['mode'],
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => false,
        ));
        return $apiContext;
    }
}
