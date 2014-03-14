<?php
namespace AppZap\Payment\Model;
use AppZap\Payment\TokenUtility;

/**
 * The Order Model
 * @package AppZap\Payment\Model
 */
class Order {

  /**
   * @var string
   */
  protected $currency_code;

  /**
   * @var string
   */
  protected $identifier;

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
  protected $record_token;

  /**
   * @var string
   */
  protected $sender_country_code;

  /**
   * @var float
   */
  protected $total_price;

  /**
   * @param string $currency_code
   */
  public function set_currency_code($currency_code) {
    $this->currency_code = $currency_code;
  }

  /**
   * @return string
   */
  public function get_currency_code() {
    return $this->currency_code;
  }

  /**
   * @param string $identifier
   */
  public function set_identifier($identifier) {
    $this->identifier = $identifier;
  }

  /**
   * @return string
   */
  public function get_identifier() {
    return $this->identifier;
  }

  /**
   * @param array $order_items
   */
  public function set_order_items($order_items) {
    $this->order_items = $order_items;
  }

  /**
   * @return array
   */
  public function get_order_items() {
    return $this->order_items;
  }

  /**
   * @param string $reason
   */
  public function set_reason($reason) {
    $this->reason = $reason;
  }

  /**
   * @return string
   */
  public function get_reason() {
    return $this->reason;
  }

  /**
   * @return string
   */
  public function get_record_token() {
    if (!$this->record_token) {
      $this->record_token = TokenUtility::generate_record_token();
    }
    return $this->record_token;
  }

  /**
   * @param string $sender_country_code
   */
  public function set_sender_country_code($sender_country_code) {
    $this->sender_country_code = $sender_country_code;
  }

  /**
   * @return string
   */
  public function get_sender_country_code() {
    return $this->sender_country_code;
  }

  /**
   * @param float $total_price
   */
  public function set_total_price($total_price) {
    $this->total_price = $total_price;
  }

  /**
   * @return float
   */
  public function get_total_price() {
    if (!isset($this->total_price) && is_array($this->order_items)) {
      $this->total_price = 0;
      foreach ($this->order_items as $order_item) {
        /** @var \AppZap\Payment\Model\OrderItem $order_item */
        $this->total_price += $order_item->get_price();
      }
    }
    return (float) $this->total_price;
  }

}