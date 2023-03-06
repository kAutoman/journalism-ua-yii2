<?php

namespace common\helpers;

/**
 * Class Optional
 *
 * @package common\helpers
 */
class Optional
{
    /**
     * @var mixed the underlying object.
     */
    protected $value;

    /**
     * Create a new optional instance.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Dynamically access a property on the underlying object.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (is_object($this->value)) {
            return $this->value->{$key};
        }
    }

    /**
     * Dynamically pass a method to the underlying object.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (is_object($this->value)) {
            return $this->value->{$method}(...$parameters);
        }
    }
}
