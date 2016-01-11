<?php

namespace Cart;

/**
 * @property string $id
 * @property int    $quantity
 * @property float  $price
 * @property float  $tax
 */
class CartItem implements \ArrayAccess, Arrayable
{
    /**
     * Cart item data.
     *
     * @var array
     *
     */
    protected $data;

    /**
     * Create a new cart item instance.
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $defaults = array(
            'quantity' => 1,
            'discount' => 0.00,
            'price' => 0.00,
            'tax' => 0.00,
        );

        $data = array_merge($defaults, $data);

        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }

    public function setDiscount($discount)
    {
        $this->data['discount'] = (float) $discount;
    }

    public function getDiscount()
    {
        return $this->data['discount'];
    }

    public function getPrice()
    {
        if (!isset($this->data['product'])) {
            return $this->price;
        }
        return $this->data['product']->getPriceForTerm($this->data['term']);
    }

    public function getPriceWithDiscount()
    {
        return $this->getPrice() - $this->getDiscount();
    }

    public function getIcannFee()
    {
        return 0;
    }

    public function getUnit()
    {
        return $this->data['product']->getUnit();
    }

    public function getProductId()
    {
        return $this->data['product']->id;
    }

    public function getTitle()
    {
        return $this->data['product']->title;
    }

    public function getDescription()
    {
        return $this->data['product']->description;
    }

    public function getSave()
    {
        return $this->data['product']->getSaveForTerm($this->data['term']) + $this->getDiscount();
    }

    public function getSavePercent()
    {
        return $this->data['product']->getSavePercentForTerm($this->data['term']);
    }

    public function getTerms()
    {
        return $this->data['product']->billing->terms;
    }

    public function getTerm()
    {
        return $this->data['term'];
    }

    public function getClass()
    {
        return strtolower(str_replace('Cart\CartItem', '', get_class($this)));
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
     * @param string $key
     *
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
     * @param string $key
     * @param mixed  $value
     *
     * @return string
     */
    public function set($key, $value)
    {
        switch ($key) {
            case 'quantity':
                $this->setCheckTypeInteger($value, $key);
                break;
            case 'price':
            case 'tax':
                $this->setCheckIsNumeric($value, $key);

                $value = (float) $value;
        }

        $this->data[$key] = $value;

        return $this->getId();
    }

    /**
     * Check the value being set is an integer.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \InvalidArgumentException
     */
    private function setCheckTypeInteger($value, $name)
    {
        if (!is_integer($value)) {
            throw new \InvalidArgumentException(sprintf('%s must be an integer.', $name));
        }
    }

    /**
     * Check the value being set is an integer.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \InvalidArgumentException
     */
    private function setCheckIsNumeric($value, $name)
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException(sprintf('%s must be numeric.', $name));
        }
    }

    /**
     * Get the total price of the cart item including tax.
     *
     * @return float
     */
    public function getTotalPrice()
    {
        return (float) ($this->getPrice() + $this->tax) * $this->quantity;
    }

    /**
     * Get the total price of the cart item excluding tax.
     *
     * @return float
     */
    public function getTotalPriceExcludingTax()
    {
        return (float) $this->getPrice() * $this->quantity;
    }

    /**
     * Get the single price of the cart item including tax.
     *
     * @return float
     */
    public function getSinglePrice()
    {
        return (float) $this->getPrice() + $this->tax;
    }

    /**
     * Get the single price of the cart item excluding tax.
     *
     * @return float
     */
    public function getSinglePriceExcludingTax()
    {
        return (float) $this->getPrice();
    }

    /**
     * Get the total tax for the cart item.
     *
     * @return float
     */
    public function getTotalTax()
    {
        return (float) $this->tax * $this->quantity;
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
            '__class' => get_class($this),
            'id' => $this->getId(),
            'data' => $this->data,
        );
    }

    /**
     * Determine if a piece of data is set on the cart item.
     *
     * @param string $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get a piece of data set on the cart item.
     *
     * @param string $key
     *
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
     * @param string $key
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Get a piece of data set on the cart item.
     *
     * @param string $key
     *
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
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Unset a piece of data from the cart item.
     *
     * @param string $key
     */
    public function __unset($key)
    {
        unset($this->data[$key]);
    }
}
