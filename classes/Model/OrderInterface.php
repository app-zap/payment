<?php
namespace AppZap\Payment\Model;

interface OrderInterface
{

    /**
     * @return string
     */
    public function getCurrencyCode();

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return array|OrderItemInterface[]
     */
    public function getOrderItems();

    /**
     * @return string
     */
    public function getPayerToken();

    /**
     * @return string
     */
    public function getReason();

    /**
     * @return string
     */
    public function getRecordToken();

    /**
     * @return string
     */
    public function getSenderCountryCode();

    /**
     * @return float
     */
    public function getTotalPrice();

}
