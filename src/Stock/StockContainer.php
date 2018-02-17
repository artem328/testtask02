<?php

namespace App\Stock;

use ArrayAccess;
use Closure;
use Iterator;
use LogicException;

class StockContainer implements Iterator, ArrayAccess
{

    /**
     * @var \App\Stock\Stock[]
     */
    private $stocks;

    /**
     * StockContainer constructor.
     * @param array $stocks
     */
    public function __construct(array $stocks = [])
    {
        foreach ($stocks as $key => $stock) {
            $this->add($stock);
        }
    }

    /**
     * @return string[]
     */
    public function getAllSymbols(): array
    {
        $symbols = [];

        foreach ($this->stocks as $stock) {
            $symbols[] = $stock->getSymbol();
        }

        return $symbols;
    }

    /**
     * @param \App\Stock\Stock $stock
     */
    public function add(Stock $stock): void
    {
        if ($this->offsetExists($stock->getSymbol())) {
            $existedStock = $this->offsetGet($stock->getSymbol());
            $existedStock->setQuantity($existedStock->getQuantity() + $stock->getQuantity());
        } else {
            $this->stocks[$stock->getSymbol()] = $stock;
        }
    }

    /**
     * @param string $symbol
     * @return \App\Stock\Stock
     */
    public function get(string $symbol): Stock
    {
        return $this->stocks[$symbol];
    }

    /**
     * @param string $symbol
     * @return bool
     */
    public function has(?string $symbol): bool
    {
        return isset($this->stocks[$symbol]);
    }

    /**
     * @param string $symbol
     */
    public function delete(string $symbol): void
    {
        unset($this->stocks[$symbol]);
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->stocks;
    }

    /**
     * @param \Closure $callback
     */
    public function each(Closure $callback): void
    {
        foreach ($this->stocks as $key => $stock) {
            $callback($stock, $key);
        }
    }

    /**
     * @param \Closure $callback
     * @return \App\Stock\StockContainer
     */
    public function map(Closure $callback): StockContainer
    {
        $newStocks = [];

        foreach ($this->stocks as $key => $stock) {
            $newStocks[] = $callback($stock, $key);
        }

        return new StockContainer($newStocks);
    }

    /**
     * @param \Closure $callback
     * @param mixed|null $value
     * @return mixed
     */
    public function reduce(Closure $callback, $value = null)
    {
        foreach ($this->stocks as $key => $stock) {
            $value = $callback($value, $stock, $key);
        }

        return $value;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return \App\Stock\Stock
     * @since 5.0.0
     */
    public function current(): Stock
    {
        return current($this->stocks);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        next($this->stocks);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return string|null scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key(): ?string
    {
        return key($this->stocks);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return $this->offsetExists($this->key());
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        reset($this->stocks);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return Stock
     * @since 5.0.0
     */
    public function offsetGet($offset): Stock
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value): void
    {
        throw new LogicException("Couldn't set value. Use addStock() method instead");
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }
}