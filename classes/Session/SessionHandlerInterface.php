<?php
namespace AppZap\Payment\Session;

interface SessionHandlerInterface
{

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function store($key, $value);

}
