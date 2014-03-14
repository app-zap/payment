<?php
namespace AppZap\Payment;

/**
 * Class TokenUtility
 *
 * To process the payment of with an external provider you need secret and cryptographically string tokens to
 * ensure that customer has paid the order.
 *
 * @package AppZap\Payment
 */
class TokenUtility {

  /**
   * @var int
   */
  protected static $key_length = 128; // 1024 bit

  /**
   * @var int
   */
  protected static $padded_order_id_length = 32;

  /**
   * Generates a random key that is used as a unique token for the order record.
   * This token is used as a salt to generate the success and abort token.
   *
   * @return string
   * @throws \Exception
   */
  public static function generate_record_token() {
    $token = bin2hex(openssl_random_pseudo_bytes(self::$key_length, $strong));
    if(!$strong) {
      throw new \Exception('openssl_random_pseudo_bytes was not able to generate a cryptographical strong key.', 1394640146);
    }
    return $token;
  }

  /**
   * Determines the success or abort url token for a certain record.
   *
   * @param array $order The order record
   * @param string $type_key Either the order_success_key or the order_error_key (from the settings.ini)
   * @return string
   * @throws \Exception
   */
  public static function get_url_token($identifier, $record_token, $type_key) {
    if (!$identifier) {
      throw new \Exception('Tried to generate a URL without record identifier.', 1394786787);
    }
    if (!$record_token) {
      throw new \Exception('Tried to generate a URL without record token. This might be a security risk.', 1394637804);
    }
    if (!$type_key) {
      throw new \Exception('Tried to generate a URL token without a type_key. Maybe order_success_key/order_error_key are not set in the settings.ini?', 1394637867);
    }
    return str_pad($identifier, self::$padded_order_id_length, '0', STR_PAD_LEFT) . hash('sha256', $record_token . $type_key);
  }

  /**
   * The URL token contains the order uid as plain text. This method reads it from the token.
   *
   * @param string $url_token
   * @return int
   */
  public static function get_order_id_from_url_token($url_token) {
    return (int) substr($url_token, 0, self::$padded_order_id_length);
  }

  /**
   * This method decides if the given token fits the order.
   *
   * @param array $order The order database record
   * @param string $token The token to check against
   * @param string $type_key Either the order_success_key or the order_error_key (from the settings.ini)
   * @return bool
   */
  public static function evaluate_url_token($identifier, $record_token, $token, $type_key) {
    $actual_token = self::get_url_token($identifier, $record_token, $type_key);
    return $token === $actual_token;
  }

}