<?php

namespace Cart;

class SessionStore implements StoreInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        return isset($_SESSION[$cartId]) ? $_SESSION[$cartId] : array();
    }

    /**
     * {@inheritdoc}
     */
    public function put($cartId, $data)
    {
        $_SESSION[$cartId] = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function flush($cartId)
    {
        unset($_SESSION[$cartId]);
    }
}
