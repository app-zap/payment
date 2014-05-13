<?php
namespace AppZap\Payment\Provider;

use Airbrake\Exception;
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

class Paypal extends Payment {

  const MODE_LIVE = 'live';
  const MODE_SANDBOX = 'sandbox';

  const PROVIDER_NAME = 'PAYPAL';

  /**
   * When you have configured the payment properly this will give you a URL that you can redirect your visitor to,
   * so that he can pay the desired amount.
   *
   * @param string $url_format
   * @return string
   */
  public function get_payment_url($url_format) {
    if (
        !is_array($this->payment_provider_auth_config[self::PROVIDER_NAME]) ||
        !isset($this->payment_provider_auth_config[self::PROVIDER_NAME]['clientid']) ||
        !isset($this->payment_provider_auth_config[self::PROVIDER_NAME]['secret'])
    ) {
      throw new \Exception('Auth Config for Provider ' . self::PROVIDER_NAME . ' is not set.', 1394795187);
    }

    $total_price = $this->order->get_total_price();
    if ($total_price == 0) {
      throw new \Exception('Total price is 0. Provider ' . self::PROVIDER_NAME . ' does not support free payments.', 1394795478);
    }

    if (!in_array($this->payment_provider_auth_config[self::PROVIDER_NAME]['mode'], array(self::MODE_SANDBOX, self::MODE_LIVE))) {
      throw new Exception('No valid API mode given.', 1399294820);
    }

    $api_context = $this->create_api_context();
    $payer = new Payer();
    $payer->setPaymentMethod("paypal");
    $amount = new Amount();
    $amount->setCurrency("EUR");
    $amount->setTotal(number_format($this->order->get_total_price(), 2));
    $transaction = new Transaction();
    $transaction->setDescription($this->order->get_reason());
    $transaction->setItemList($this->getItemList());
    $transaction->setAmount($amount);
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($this->get_success_url($url_format));
    $redirectUrls->setCancelUrl($this->get_abort_url($url_format));
    $payment = new \PayPal\Api\Payment();
    $payment->setIntent('sale');
    $payment->setPayer($payer);
    $payment->setRedirectUrls($redirectUrls);
    $payment->setTransactions(array($transaction));
    $payment->create($api_context);
    $_SESSION['app-zap/payment/payment_id'] = $payment->getId();
    $payment_url = '';
    foreach ($payment->getLinks() as $link) {
      /** @var \PayPal\Api\Links $link */
      if($link->getRel() == 'approval_url') {
        $payment_url = $link->getHref();
      }
    }
    return $payment_url . '&useraction=commit';
  }

  public function execute() {
    $querystring = $_SERVER['QUERY_STRING'];
    $params = array();
    parse_str($querystring, $params);
    if ($params['PayerID']) {
      $api_context = $this->create_api_context();
      $paymentId = $_SESSION['app-zap/payment/payment_id'];
      $payment = \PayPal\Api\Payment::get($paymentId, $api_context);
      $execution = new PaymentExecution();
      $execution->setPayerId($params['PayerID']);
      $returned_state = $payment->execute($execution, $api_context);
      if ($returned_state->getIntent() !== 'sale') {
        throw new \Exception('Paypal Payment execution failed', 1399884990);
      }
    } else {
      throw new \Exception('Paypal Payment execution failed: No PayerID given.', 1399893312);
    }
  }

  /**
   * @return ItemList
   */
  protected function getItemList() {
    $items = array();
    foreach ($this->order->get_order_items() as $order_item) {
      /** @var \AppZap\Payment\Model\OrderItem $order_item */
      $item = new Item();
      $item->setName($order_item->get_title());
      $item->setQuantity($order_item->get_quantity());
      $item->setSku($order_item->get_article_number());
      $item->setPrice(number_format($order_item->get_price() / $order_item->get_quantity(), 2));
      $item->setCurrency("EUR");
      $items[] = $item;
    }
    $itemList = new ItemList();
    $itemList->setItems($items);
    return $itemList;
  }

  /**
   * @return ApiContext
   */
  protected function create_api_context() {
    $api_context = new ApiContext(new OAuthTokenCredential(
        $this->payment_provider_auth_config[self::PROVIDER_NAME]['clientid'],
        $this->payment_provider_auth_config[self::PROVIDER_NAME]['secret']
    ));
    $api_context->setConfig(array(
        'mode' => $this->payment_provider_auth_config[self::PROVIDER_NAME]['mode'],
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => false,
    ));
    return $api_context;
  }
}