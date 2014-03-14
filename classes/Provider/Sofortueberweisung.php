<?php
namespace AppZap\Payment\Provider;

use AppZap\Payment\Payment;

class Sofortueberweisung extends Payment {

  const PROVIDER_NAME = 'SOFORTUEBERWEISUNG';

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
      !isset($this->payment_provider_auth_config[self::PROVIDER_NAME]['configkey'])
    ) {
      throw new \Exception('Auth Config for Provider ' . self::PROVIDER_NAME . ' is not set.', 1394785987);
    }

    $total_price = $this->order->get_total_price();
    if ($total_price == 0) {
      throw new \Exception('Total price is 0. Provider ' . self::PROVIDER_NAME . ' does not support free payments.', 1394786580);
    }

    $sofort = new \Sofortueberweisung($this->payment_provider_auth_config[self::PROVIDER_NAME]['configkey']);
    $sofort->setAmount($total_price);
    $sofort->setCurrencyCode('EUR');
    $sofort->setSenderCountryCode('DE');
    $sofort->setReason($this->order->get_reason());
    $sofort->setSuccessUrl($this->get_success_url($url_format));
    $sofort->setAbortUrl($this->get_abort_url($url_format));
    $sofort->sendRequest();
    $url = $sofort->getPaymentUrl();
    return $url;
  }
}