<?php
namespace AppZap\Payment\Model;

use AppZap\Payment\TokenUtility;

/**
 * The Order Model
 */
class Order implements OrderInterface
{

    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var array|OrderItem[]
     */
    protected $orderItems;

    /**
     * @var string
     */
    protected $reason;

    /**
     * @var string
     */
    protected $recordToken;

    /**
     * @var string
     */
    protected $senderCountryCode;

    /**
     * @var float
     */
    protected $totalPrice;

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param array $orderItems
     */
    public function setOrderItems($orderItems)
    {
        $this->orderItems = $orderItems;
    }

    /**
     * @return array
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function getRecordToken()
    {
        if (!$this->recordToken) {
            $this->recordToken = TokenUtility::generateRecordToken();
        }
        return $this->recordToken;
    }

    /**
     * @param string $senderCountryCode
     */
    public function setSenderCountryCode($senderCountryCode)
    {
        $this->senderCountryCode = $senderCountryCode;
    }

    /**
     * @return string
     */
    public function getSenderCountryCode()
    {
        return $this->senderCountryCode;
    }

    /**
     * @param float $totalPrice
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        if (!isset($this->totalPrice) && is_array($this->orderItems)) {
            $this->totalPrice = 0;
            foreach ($this->orderItems as $orderItem) {
                $this->totalPrice += $orderItem->getPrice();
            }
        }
        return (float)$this->totalPrice;
    }

}
