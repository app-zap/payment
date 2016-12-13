<?php
namespace AppZap\Payment;

/**
 * Class TokenUtility
 *
 * To process the payment of with an external provider you need secret and cryptographically string tokens to
 * ensure that customer has paid the order.
 */
class TokenUtility
{

    /**
     * @var int
     */
    protected static $keyLength = 128; // 1024 bit

    /**
     * @var int
     */
    protected static $paddedOrderIdLength = 32;

    /**
     * Generates a random key that is used as a unique token for the order record.
     * This token is used as a salt to generate the success and abort token.
     *
     * @return string
     * @throws \Exception
     */
    public static function generateRecordToken()
    {
        $token = bin2hex(openssl_random_pseudo_bytes(self::$keyLength, $strong));
        if (!$strong) {
            throw new \Exception('openssl_random_pseudo_bytes was not able to generate a cryptographical strong key.', 1394640146);
        }
        return $token;
    }

    /**
     * Determines the success or abort url token for a certain record.
     *
     * @param string $identifier The order identifier
     * @param string $recordToken
     * @param string $typeKey Either the orderSuccessKey or the orderErrorKey
     * @return string
     * @throws \Exception
     */
    public static function getUrlToken($identifier, $recordToken, $typeKey)
    {
        if (!$identifier) {
            throw new \Exception('Tried to generate a URL without record identifier.', 1394786787);
        }
        if (!$recordToken) {
            throw new \Exception('Tried to generate a URL without record token. This might be a security risk.', 1394637804);
        }
        if (!$typeKey) {
            throw new \Exception('Tried to generate a URL token without a typeKey.', 1394637867);
        }
        return str_pad($identifier, self::$paddedOrderIdLength, '0', STR_PAD_LEFT) . hash('sha256', $recordToken . $typeKey);
    }

    /**
     * The URL token contains the order uid as plain text. This method reads it from the token.
     *
     * @param string $urlToken
     * @return int
     */
    public static function getOrderIdFromUrlToken($urlToken)
    {
        return (int) substr($urlToken, 0, self::$paddedOrderIdLength);
    }

    /**
     * This method decides if the given token fits the order.
     *
     * @param string $identifier The order database record
     * @param string $recordToken The order database record
     * @param string $token The token to check against
     * @param string $typeKey Either the orderSuccessKey or the orderErrorKey (from the settings.ini)
     * @return bool
     */
    public static function evaluateUrlToken($identifier, $recordToken, $token, $typeKey)
    {
        if (!$identifier || !$recordToken || !$token) {
            return false;
        }
        return $token === self::getUrlToken($identifier, $recordToken, $typeKey);
    }
}
