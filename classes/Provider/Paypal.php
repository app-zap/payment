<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Payment;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class Paypal extends Payment {

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

    $api_context = new ApiContext(new OAuthTokenCredential(
        $this->payment_provider_auth_config[self::PROVIDER_NAME]['clientid'],
        $this->payment_provider_auth_config[self::PROVIDER_NAME]['secret']
    ));
    $api_context->setConfig(array(
        'mode' => 'sandbox',
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => false,
    ));
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
    $payment->setIntent("sale");
    $payment->setPayer($payer);
    $payment->setRedirectUrls($redirectUrls);
    $payment->setTransactions(array($transaction));
    $payment->create($api_context);
    $payment_url = '';
    foreach ($payment->getLinks() as $link) {
      /** @var \PayPal\Api\Links $link */
      if($link->getRel() == 'approval_url') {
        $payment_url = $link->getHref();
      }
    }
    return $payment_url;
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
}