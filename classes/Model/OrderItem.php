<?php
namespace AppZap\Payment\Model;

/**
 * The OrderItem Model
 * @package AppZap\Payment\Model
 */
class OrderItem {

  /**
   * @var string
   */
  protected $article_number;

  /**
   * @var string
   */
  protected $currency;

  /**
   * @var float
   */
  protected $price;

  /**
   * @var float
   */
  protected $quantity;

  /**
   * @var string
   */
  protected $title;

  /**
   * @param string $article_number
   */
  public function set_article_number($article_number) {
    $this->article_number = $article_number;
  }

  /**
   * @return string
   */
  public function get_article_number() {
    return $this->article_number;
  }

  /**
   * @param string $currency
   */
  public function set_currency($currency) {
    $this->currency = $currency;
  }

  /**
   * @return string
   */
  public function get_currency() {
    return $this->currency;
  }

  /**
   * @param float $price
   */
  public function set_price($price) {
    $this->price = $price;
  }

  /**
   * @return float
   */
  public function get_price() {
    return $this->price;
  }

  /**
   * @param float $quantity
   */
  public function set_quantity($quantity) {
    $this->quantity = $quantity !== 0 ? $quantity : 1;
  }

  /**
   * @return float
   */
  public function get_quantity() {
    return $this->quantity;
  }

  /**
   * @param string $title
   */
  public function set_title($title) {
    $this->title = $title;
  }

  /**
   * @return string
   */
  public function get_title() {
    return $this->title;
  }

}