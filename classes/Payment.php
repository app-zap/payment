<?php

namespace AppZap\Payment;

/**
 * Class Payment
 *
 * The main class used to configure and prepare the payment.
 *
 * See: ->get_payment_url()
 *
 * @package AppZap\Payment
 */
class Payment {

  /**
   * @var string
   */
  protected $abort_key;

  /**
   * @var bool
   */
  protected $debug_token_urls = false;

  /**
   * @var \AppZap\Payment\Model\Order
   */
  protected $order;

  /**
   * @var string
   */
  protected $payment_provider;

  /**
   * @var string
   */
  protected $success_key;

  /**
   * @param string $abort_key
   */
  public function set_abort_key($abort_key) {
    $this->abort_key = $abort_key;
  }

  /**
   * @return string
   */
  public function get_abort_key() {
    return $this->abort_key;
  }

  /**
   * @return string
   */
  public function get_abort_url() {
    $abort_url = '';
    return $abort_url;
  }

  /**
   * @param boolean $debug_token_urls
   */
  public function set_debug_token_urls($debug_token_urls) {
    $this->debug_token_urls = $debug_token_urls;
  }

  /**
   * @return boolean
   */
  public function get_debug_token_urls() {
    return $this->debug_token_urls;
  }

  /**
   * @param \AppZap\Payment\Model\Order $order
   */
  public function set_order($order) {
    $this->order = $order;
  }

  /**
   * @return \AppZap\Payment\Model\Order
   */
  public function get_order() {
    return $this->order;
  }

  /**
   * @param string $success_key
   */
  public function set_success_key($success_key) {
    $this->success_key = $success_key;
  }

  /**
   * @return string
   */
  public function get_success_key() {
    return $this->success_key;
  }

  /**
   * @param string $payment_provider
   */
  public function set_payment_provider($payment_provider) {
    $this->payment_provider = $payment_provider;
  }

  /**
   * @return string
   */
  public function get_payment_provider() {
    return $this->payment_provider;
  }

  /**
   * When you have configured the payment properly this will give you a URL that you can redirect your visitor to,
   * so that he can pay the desired amount.
   *
   * @return string
   */
  public function get_payment_url() {
    $payment_url = '';
    return $payment_url;
  }

  /**
   * @return string
   */
  public function get_success_url() {
    $success_url = '';
    return $success_url;
  }

}

?>