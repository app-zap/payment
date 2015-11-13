<?php
namespace AppZap\Payment\Session;

class SessionHandler implements SessionHandlerInterface
{

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $_SESSION['app-zap/payment/' . $key];
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function store($key, $value)
    {
        $_SESSION['app-zap/payment/' . $key] = $value;
    }
}
