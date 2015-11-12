<?php
namespace AppZap\Payment\Model;

interface OrderItemInterface
{

    /**
     * @return string
     */
    public function getArticleNumber();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @return float
     */
    public function getQuantity();

    /**
     * @return string
     */
    public function getTitle();

}
