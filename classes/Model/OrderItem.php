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
  public function setArticleNumber($article_number) {
    $this->article_number = $article_number;
  }

  /**
   * @return string
   */
  public function getArticleNumber() {
    return $this->article_number;
  }

  /**
   * @param string $currency
   */
  public function setCurrency($currency) {
    $this->currency = $currency;
  }

  /**
   * @return string
   */
  public function getCurrency() {
    return $this->currency;
  }

  /**
   * @param float $price
   */
  public function setPrice($price) {
    $this->price = $price;
  }

  /**
   * @return float
   */
  public function getPrice() {
    return $this->price;
  }

  /**
   * @param float $quantity
   */
  public function setQuantity($quantity) {
    $this->quantity = $quantity;
  }

  /**
   * @return float
   */
  public function getQuantity() {
    return $this->quantity;
  }

  /**
   * @param string $title
   */
  public function setTitle($title) {
    $this->title = $title;
  }

  /**
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }

}