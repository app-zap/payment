<?php

namespace AppZap\Payment;
use AppZap\Payment\Provider\Paypal;
use AppZap\Payment\Provider\Sofortueberweisung;

/**
 * Class Payment
 *
 * The main class used to configure and prepare the payment.
 *
 * See: ->get_payment_url()
 *
 * @package AppZap\Payment
 */
abstract class Payment {

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
   * @var array
   */
  protected $payment_provider_auth_config;

  /**
   * @var string
   */
  protected $success_key;

  /**
   * @param $payment_provider
   * @return \AppZap\Payment\Payment
   * @throws \InvalidArgumentException
   */
  public static function get_instance($payment_provider) {

    $supported_payment_providers = array(
      Paypal::PROVIDER_NAME => '\AppZap\Payment\Provider\Paypal',
      Sofortueberweisung::PROVIDER_NAME => '\AppZap\Payment\Provider\Sofortueberweisung',
    );

    if (in_array($payment_provider, array_keys($supported_payment_providers))) {
      return new $supported_payment_providers[$payment_provider];
    } else {
      throw new \InvalidArgumentException('Payment provider ' . htmlentities($payment_provider) . ' is not supported.');
    }
  }

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
   * @param array $payment_provider_auth_config
   */
  public function set_payment_provider_auth_config($payment_provider_auth_config) {
    $this->payment_provider_auth_config = $payment_provider_auth_config;
  }

  /**
   * @return array
   */
  public function get_payment_provider_auth_config() {
    return $this->payment_provider_auth_config;
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
   * When you have configured the payment properly this will give you a URL that you can redirect your visitor to,
   * so that he can pay the desired amount.
   *
   * @param string $url_format
   * @return string
   */
  abstract public function get_payment_url($url_format);

  /**
   * @param string $url_format
   * @return string
   */
  public function get_abort_url($url_format) {
    return sprintf(
        $url_format,
        TokenUtility::get_url_token($this->order->get_identifier(), $this->order->get_record_token(), $this->get_abort_key())
    );
  }

  /**
   * @param string $url_format
   * @return string
   */
  public function get_success_url($url_format) {
    return sprintf(
        $url_format,
        TokenUtility::get_url_token($this->order->get_identifier(), $this->order->get_record_token(), $this->get_success_key())
    );
  }

  public function execute() {
  }

}

?>