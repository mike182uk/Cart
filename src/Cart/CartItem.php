<?php

namespace Cart;

use Cart\Arrayable;
use ArrayAccess;
use InvalidArgumentException;

class CartItem implements ArrayAccess, Arrayable
{
    /**
     * Cart item data.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new cart item instance.
     *
     * @param  array $data
     * @return void
     */
    public function __construct($data = array())
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }

        // make sure quantity is set
        if ( ! isset($this->quantity)) {
            $this->quantity = 1;
        }

        // make sure price is set
        if ( ! isset($this->price)) {
            $this->price = 0.00;
        }

        // make sure tax is set
        if ( ! isset($this->tax)) {
            $this->tax = 0.00;
        }
    }

    /**
     * Get the cart item id.
     *
     * @return string
     */
    public function getId()
    {
        // keys to ignore in the hashing process
        $ignoreKeys = array('quantity');

        // data to use for the hashing process
        $hashData = $this->data;
        foreach ($ignoreKeys as $key) {
            unset($hashData[$key]);
        }

        $hash = sha1(serialize($hashData));

        return $hash;
    }

    /**
     * Get a piece of data set on the cart item.
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        switch ($key) {
            case 'id':
                return $this->getId();
            default:
                return $this->data[$key];
        }
    }

    /**
     * Set a piece of data on the cart item.
     *
     * @param  string                   $key
     * @param  mixed                    $value
     * @return string
     * @throws InvalidArgumentException
     */
    public function set($key, $value)
    {
        switch ($key) {
            case 'quantity':
                if ( ! is_integer($value)) {
                    throw new InvalidArgumentException('Quantity must be an integer.');
                }
            break;
            case 'price':
            case 'tax':
                if ( ! is_numeric($value)) {
                    throw new InvalidArgumentException(sprintf('%s must be numeric', $key));
                }

                $value = (float) $value;
            break;
        }

        $this->data[$key] = $value;

        return $this->getId();
    }

    /**
     * Get the total price of the cart item including tax.
     *
     * @return float
     */
    public function getTotalPrice()
    {
        return (float) ($this->price + $this->tax) * $this->quantity;
    }

    /**
     * Get the total price of the cart item excluding tax.
     *
     * @return float
     */
    public function getTotalPriceExcludingTax()
    {
        return (float) $this->price * $this->quantity;
    }

    /**
     * Get the single price of the cart item including tax.
     *
     * @return float
     */
    public function getSinglePrice()
    {
        return (float) $this->price + $this->tax;
    }

    /**
     * Get the single price of the cart item excluding tax.
     *
     * @return float
     */
    public function getSinglePriceExcludingTax()
    {
        return (float) $this->price;
    }

    /**
     * Get the total tax for the cart item.
     *
     * @return float
     */
    public function getTotalTax()
    {
        return (float) ($this->tax * $this->quantity);
    }

    /**
     * Get the single tax value of the cart item.
     *
     * @return float
     */
    public function getSingleTax()
    {
        return (float) $this->tax;
    }

    /**
     * Export the cart item as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'data' => $this->data
        );
    }

    /**
     * Determine if a piece of data is set on the cart item.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get a piece of data set on the cart item.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a piece of data on the cart item.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a piece of data from the cart item.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Get a piece of data set on the cart item.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Set a piece of data on the cart item.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Determine if a piece of data is set on the cart item.
     *
     * @param  string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Unset a piece of data from the cart item.
     *
     * @param  string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->data[$key]);
    }
}
