<?php
/**
 * JSON Signer and Verifier
 *
 * Copyright (c) 2017 Adam Prickett
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @author Adam Prickett <adam.prickett@ampersa.co.uk>
 * @license MIT
 */
namespace Ampersa\JsonSigner\Support;

use InvalidArgumentException;

class JsonCollection
{
    protected $items = [];

    /**
     * Construct the collection, accepting JsonCollection, Object,
     * Array or valid JSON String
     * @param string|array|object|JsonCollection $input
     */
    public function __construct($input = null)
    {
        if ($input instanceof JsonCollection) {
            $this->items = $input->all();
        } else if (is_object($input) or is_array($input)) {
            $this->items = (array) $input;
        } else if (is_string($input)) {
            $decoded = json_decode($input, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('The string provided is not valid JSON');
            }

            $this->items = $decoded;
        }
    }

    /**
     * Return all the Collections items
     * @return array
     */
    public function all()
    {
        return $this->toArray();
    }

    /**
     * Returns a count of the Collection items (top-level)
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Check whether a key exists on the Collection
     * @param  string $key
     * @return bool
     */
    public function exists($key)
    {
        return isset($this->items[$key]);
    }

    /**
     * Forget a key from the Collection
     * @param  string $key
     * @return self
     */
    public function forget($key)
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * Return a value for a key from the Collection
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        return false;
    }

    /**
     * Set a value on the Collection for a key
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->items[$key] = $value;

        return $this;
    }

    /**
     * Sort the collection by values
     * @param  boolean $desc
     * @return self
     */
    public function sort($desc = false)
    {
        if ($desc) {
            arsort($this->items);
        } else {
            asort($this->items);
        }

        return $this;
    }

    /**
     * Sort the collection by keys
     * @param  boolean $desc
     * @return self
     */
    public function sortKeys($desc = false)
    {
        if ($desc) {
            krsort($this->items);
        } else {
            ksort($this->items);
        }

        return $this;
    }

    /**
     * Return the collection contents as JSON
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->items);
    }

    /**
     * Return the collection contents as an Array
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Magic function to return JSON when requested as a string
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Magic function to retrieve a value from the collection
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Magic function to set a value on the collection
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * Magic function to perform isset() on the collection
     * @param  string  $key
     * @return boolean
     */
    public function __isset($key)
    {
        return $this->exists($key);
    }
}
