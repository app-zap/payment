<?php
namespace AppZap\Payment\Model;

/**
 * The Order Model
 * @package AppZap\Payment\Model
 */
class Order {

  /**
   * @var string
   */
  protected $abort_key;

  /**
   * @var string
   */
  protected $currency_code;

  /**
   * @var array
   */
  protected $order_items;

  /**
   * @var string
   */
  protected $reason;

  /**
   * @var string
   */
  protected $sender_country_code;

  /**
   * @var string
   */
  protected $success_key;

  /**
   * @var float
   */
  protected $total_price;

  /**
   * @param string $abort_key
   */
  public function setAbortKey($abort_key) {
    $this->abort_key = $abort_key;
  }

  /**
   * @return string
   */
  public function getAbortKey() {
    return $this->abort_key;
  }

  /**
   * @param string $currency_code
   */
  public function setCurrencyCode($currency_code) {
    $this->currency_code = $currency_code;
  }

  /**
   * @return string
   */
  public function getCurrencyCode() {
    return $this->currency_code;
  }

  /**
   * @param array $order_items
   */
  public function setOrderItems($order_items) {
    $this->order_items = $order_items;
  }

  /**
   * @return array
   */
  public function getOrderItems() {
    return $this->order_items;
  }

  /**
   * @param string $reason
   */
  public function setReason($reason) {
    $this->reason = $reason;
  }

  /**
   * @return string
   */
  public function getReason() {
    return $this->reason;
  }

  /**
   * @param string $sender_country_code
   */
  public function setSenderCountryCode($sender_country_code) {
    $this->sender_country_code = $sender_country_code;
  }

  /**
   * @return string
   */
  public function getSenderCountryCode() {
    return $this->sender_country_code;
  }

  /**
   * @param string $success_key
   */
  public function setSuccessKey($success_key) {
    $this->success_key = $success_key;
  }

  /**
   * @return string
   */
  public function getSuccessKey() {
    return $this->success_key;
  }

  /**
   * @param float $total_price
   */
  public function setTotalPrice($total_price) {
    $this->total_price = $total_price;
  }

  /**
   * @return float
   */
  public function getTotalPrice() {
    return $this->total_price;
  }

}