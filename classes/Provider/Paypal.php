<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Payment;

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

  }
}